<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    /**
     * Return the next level of options for the cascading selection.
     *
     * Forward flow (default / admin):
     *   ?nabl=            -> disciplines (Sample Name / Discipline Group)
     *   &discipline=      -> materials
     *   &material=        -> parameters (final selectable tests)
     *
     * Reverse flow (public form):
     *   ?parameter=       -> disciplines containing the parameter
     *   &discipline=      -> NABL / NON NABL types for the parameter
     *   &nabl=            -> resolved distinct tests for the combination
     */
    public function options(Request $request)
    {
        $parameter = $request->input('parameter');

        if (!empty($parameter)) {
            return $this->reverseOptions($parameter, $request);
        }

        if ($request->filled('nabl') || $request->filled('discipline') || $request->filled('material')) {
            return $this->forwardOptions($request);
        }

        // Root of the parameter-first (reverse) cascade: list all distinct parameters.
        $options = LabTest::active()
            ->whereNotNull('parameter')
            ->where('parameter', '!=', '')
            ->select('parameter')
            ->distinct()
            ->orderBy('parameter')
            ->pluck('parameter')
            ->values()
            ->all();

        return response()->json([
            'level' => 'parameter',
            'options' => $options,
        ]);
    }

    protected function reverseOptions(string $parameter, Request $request)
    {
        $discipline = $request->input('discipline');
        $nabl = $request->input('nabl');

        $query = LabTest::active()->where('parameter', $parameter);

        if (!empty($discipline)) {
            $query->where('discipline', $discipline);
        }

        // Level 1: disciplines available for this parameter.
        if (empty($discipline)) {
            $options = (clone $query)
                ->select('discipline')
                ->distinct()
                ->orderBy('discipline')
                ->get()
                ->pluck('discipline')
                ->filter(fn ($d) => $d !== '')
                ->values()
                ->all();

            return response()->json([
                'level' => 'discipline',
                'parameter' => $parameter,
                'options' => $options,
            ]);
        }

        // Level 2: NABL / NON NABL types for this parameter + discipline.
        if (empty($nabl)) {
            $options = (clone $query)
                ->select('nabl_type')
                ->distinct()
                ->orderBy('nabl_type')
                ->get()
                ->pluck('nabl_type')
                ->filter(fn ($n) => $n !== '')
                ->values()
                ->all();

            return response()->json([
                'level' => 'nabl',
                'parameter' => $parameter,
                'discipline' => $discipline,
                'options' => $options,
            ]);
        }

        $query->where('nabl_type', $nabl);

        // Level 3: resolved tests, deduplicated so method/price/quantity/protocol
        // variants collapse into a single selectable test.
        $rows = $query->orderBy('id')->get();

        $unique = [];
        foreach ($rows as $t) {
            $key = implode('|', [
                (string) $t->method,
                (string) $t->charges_per_sample,
                (string) $t->sample_quantity,
                (string) $t->protocol_no,
            ]);
            if (!isset($unique[$key])) {
                $unique[$key] = $t;
            }
        }

        $tests = collect($unique)->sortBy(fn ($t) => $t->id)->values()
            ->map(fn ($t) => [
                'id' => $t->id,
                'nabl_type' => $t->nabl_type,
                'discipline' => $t->discipline,
                'material' => $t->material,
                'parameter' => $t->parameter,
                'method' => $t->method,
                'sample_quantity' => $t->sample_quantity,
                'lead_time' => $t->lead_time,
                'charges_per_sample' => $t->charges_per_sample,
                'protocol_no' => $t->protocol_no,
                'nabl_range' => $t->nabl_range,
                'limit_of_quantification' => $t->limit_of_quantification,
                'remarks' => $t->remarks,
                'protocol_link' => $t->protocol_link,
            ]);

        return response()->json([
            'level' => 'test',
            'parameter' => $parameter,
            'discipline' => $discipline,
            'nabl' => $nabl,
            'tests' => $tests->all(),
        ]);
    }

    protected function forwardOptions(Request $request)
    {
        $nabl = $request->input('nabl');
        $discipline = $request->input('discipline');
        $material = $request->input('material');

        $query = LabTest::active();

        if (!empty($nabl)) {
            $query->where('nabl_type', $nabl);
        }

        // Level 1: disciplines, grouped by NABL type
        if (empty($discipline)) {
            $rows = $query
                ->select('nabl_type', 'discipline')
                ->distinct()
                ->orderBy('nabl_type')
                ->orderBy('discipline')
                ->get();

            $groups = [];
            foreach ($rows as $row) {
                if ($row->discipline === '') {
                    continue;
                }
                $groups[$row->nabl_type][] = $row->discipline;
            }

            // If a specific NABL type was requested, return a flat discipline list.
            if (!empty($nabl)) {
                return response()->json([
                    'level' => 'discipline',
                    'nabl' => $nabl,
                    'options' => $groups[$nabl] ?? [],
                ]);
            }

            return response()->json([
                'level' => 'nabl',
                'options' => array_keys($groups),
                'groups' => $groups,
            ]);
        }

        $query->where('discipline', $discipline);

        // Level 2: materials (under the chosen discipline / nabl type)
        if (empty($material)) {
            $materials = $query
                ->select('material')
                ->whereNotNull('material')
                ->where('material', '!=', '')
                ->distinct()
                ->orderBy('material')
                ->pluck('material');

            return response()->json([
                'level' => 'material',
                'options' => $materials->values()->all(),
            ]);
        }

        $query->where('material', $material);

        // Level 3: parameters (final selectable tests)
        $tests = $query->orderBy('parameter')->get();

        return response()->json([
            'level' => 'parameter',
            'options' => $tests->map(fn ($t) => [
                'id' => $t->id,
                'nabl_type' => $t->nabl_type,
                'discipline' => $t->discipline,
                'material' => $t->material,
                'parameter' => $t->parameter,
                'method' => $t->method,
                'sample_quantity' => $t->sample_quantity,
                'lead_time' => $t->lead_time,
                'protocol_no' => $t->protocol_no,
                'nabl_range' => $t->nabl_range,
                'limit_of_quantification' => $t->limit_of_quantification,
                'remarks' => $t->remarks,
                'protocol_link' => $t->protocol_link,
            ]),
        ]);
    }
}
