<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    /**
     * Return the next level of options for the cascading selection.
     *
     * Levels:
     *   ?nabl=            -> disciplines (Sample Name / Discipline Group)
     *   &discipline=      -> materials
     *   &material=        -> parameters (final selectable tests)
     */
    public function options(Request $request)
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
