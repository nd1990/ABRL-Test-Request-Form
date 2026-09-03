<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Services\AuditLogService;
use App\Services\LabTestCatalogService;
use Illuminate\Http\Request;

class AdminServicesController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    protected function buildIndexQuery(Request $request)
    {
        $query = LabTest::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('parameter', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhere('discipline', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%")
                    ->orWhere('protocol_no', 'like', "%{$search}%")
                    ->orWhere('s_no', '=', (int) $search);
            });
        }

        foreach (['nabl_type', 'discipline', 'material', 'parameter'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        return $query->orderBy('s_no');
    }

    public function index(Request $request)
    {
        $labTests = $this->buildIndexQuery($request)->paginate(20, ['*'], 'page')->withQueryString();

        return view('admin.lab-tests.index', [
            'labTests' => $labTests,
            'nablTypes' => LabTest::query()->where('nabl_type', '!=', '')->distinct()->orderBy('nabl_type')->pluck('nabl_type'),
            'disciplines' => LabTest::query()->where('discipline', '!=', '')->distinct()->orderBy('discipline')->pluck('discipline'),
            'materials' => LabTest::query()->whereNotNull('material')->where('material', '!=', '')->distinct()->orderBy('material')->pluck('material'),
            'parameters' => LabTest::query()->where('parameter', '!=', '')->distinct()->orderBy('parameter')->pluck('parameter'),
            'filters' => $request->only(['search', 'nabl_type', 'discipline', 'material', 'parameter', 'status']),
        ]);
    }

    public function data(Request $request)
    {
        $labTests = $this->buildIndexQuery($request)
            ->paginate(20, ['*'], 'page', max(1, (int) $request->input('page', 1)));

        $data = [
            'rows' => view('admin.lab-tests._rows', ['labTests' => $labTests])->render(),
            'pagination' => view('admin.lab-tests._pagination', ['labTests' => $labTests])->render(),
            'total' => $labTests->total(),
            'from' => $labTests->firstItem() ?: 0,
            'to' => $labTests->lastItem() ?: 0,
            'current_page' => $labTests->currentPage(),
            'last_page' => $labTests->lastPage(),
            'has_pages' => $labTests->hasPages(),
            'count' => $labTests->count(),
            'ids' => $labTests->map(fn($t) => $t->id)->values()->all(),
        ];

        return response()->json($data);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $admin = app('admin');
        $ids = $validated['ids'];

        $deleted = LabTest::whereIn('id', $ids)->delete();

        $this->auditLog->log($admin, 'lab_tests.bulk_deleted', 'LabTest', null, ['ids' => $ids], ['count' => $deleted], $request);

        return response()->json([
            'message' => $deleted . ' service(s) deleted successfully.',
            'deleted' => $deleted,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $labTest = LabTest::create($data);
        $this->auditLog->log(app('admin'), 'lab_tests.created', 'LabTest', $labTest->id, null, $labTest->toArray(), $request);

        return back()->with('success', 'Lab test "' . $labTest->parameter . '" created successfully.');
    }

    public function update(Request $request, LabTest $labTest)
    {
        $data = $this->validated($request);
        $old = $labTest->toArray();

        $labTest->update($data);
        $this->auditLog->log(app('admin'), 'lab_tests.updated', 'LabTest', $labTest->id, $old, $labTest->toArray(), $request);

        return back()->with('success', 'Lab test "' . $labTest->parameter . '" updated successfully.');
    }

    public function destroy(Request $request, LabTest $labTest)
    {
        $this->auditLog->log(app('admin'), 'lab_tests.deleted', 'LabTest', $labTest->id, $labTest->toArray(), null, $request);

        $name = $labTest->parameter;
        $labTest->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Service "' . $name . '" deleted successfully.',
            ]);
        }

        return back()->with('success', 'Lab test "' . $name . '" deleted successfully.');
    }

    public function sync(Request $request)
    {
        $admin = app('admin');

        try {
            $count = app(LabTestCatalogService::class)->reSync();
        } catch (\Throwable $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }

        $this->auditLog->log($admin, 'lab_tests.synced', 'LabTest', null, null, ['count' => $count], $request);

        return back()->with('success', "Google Sheet synced. {$count} lab tests imported.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $admin = app('admin');

        $csv = (string) $request->file('file')->get();

        if (trim($csv) === '') {
            return back()->with('error', 'The uploaded file is empty.');
        }

        try {
            $count = app(LabTestCatalogService::class)->importFromString($csv);
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $this->auditLog->log($admin, 'lab_tests.imported', 'LabTest', null, null, ['count' => $count], $request);

        return back()->with('success', "CSV imported successfully. {$count} lab tests imported.");
    }

    protected function validated(Request $request): array
    {
        $validated = $request->validate([
            's_no' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'nabl_type' => ['required', 'in:NABL,NON NABL'],
            'discipline' => ['required', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'parameter' => ['required', 'string', 'max:255'],
            'method' => ['nullable', 'string', 'max:255'],
            'sample_quantity' => ['nullable', 'string', 'max:255'],
            'lead_time' => ['nullable', 'string', 'max:255'],
            'charges_per_sample' => ['nullable', 'numeric', 'min:0'],
            'gst_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'protocol_no' => ['nullable', 'string', 'max:255'],
            'nabl_range' => ['nullable', 'string', 'max:255'],
            'limit_of_quantification' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'protocol_link' => ['nullable', 'url', 'max:500'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['discipline'] = trim($validated['discipline']);

        return $validated;
    }
}
