<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use App\Services\BackupService;
use Illuminate\Http\Request;

class AdminBackupController extends Controller
{
    public function __construct(
        protected BackupService $backup,
        protected AuditLogService $auditLog
    ) {}

    public function index()
    {
        $settings = $this->backup->settings();
        $backups = $this->backup->list();

        return view('admin.backups.index', compact('settings', 'backups'));
    }

    public function store(Request $request)
    {
        $admin = app('admin');

        try {
            $result = $this->backup->create('manual');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }

        $this->auditLog->backupCreated($admin, $result['filename'], $request);

        return redirect()->route('admin.backups.index')
            ->with('success', 'Manual backup created: ' . $result['filename'] . ' (' . $result['size_human'] . ')');
    }

    public function download(string $backup)
    {
        try {
            $file = $this->backup->file($backup);
        } catch (\Throwable $e) {
            abort(404);
        }

        return response()->download($file['path'], $file['filename']);
    }

    public function destroy(string $backup)
    {
        $admin = app('admin');
        $deleted = $this->backup->delete($backup);

        if (!$deleted) {
            return back()->with('error', 'Backup could not be deleted.');
        }

        $this->auditLog->backupDeleted($admin, basename($backup), request());

        return back()->with('success', 'Backup deleted.');
    }

    public function restore(string $backup)
    {
        $admin = app('admin');

        try {
            $file = $this->backup->file($backup);
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup not found.');
        }

        // Safety net: snapshot the current database before overwriting it.
        try {
            $this->backup->create('prerestore');
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore aborted: could not create a safety backup of the current database first. ' . $e->getMessage());
        }

        try {
            $this->backup->restore($file['filename']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        $this->auditLog->backupRestored($admin, $file['filename'], request());

        return redirect()->route('admin.backups.index')
            ->with('success', 'Database restored from ' . $file['filename'] . '. A safety backup of the previous state was saved as a Pre-restore backup.');
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'frequency' => ['required', 'in:off,daily,weekly,monthly'],
            'time' => ['required', 'date_format:H:i'],
            'keep' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $old = $this->backup->settings();
        $new = [
            'frequency' => $data['frequency'],
            'time' => $data['time'],
            'keep' => (string) $data['keep'],
        ];

        \App\Models\Setting::setGroup('backup', $new);

        $this->auditLog->settingsChanged(app('admin'), 'backup', $old, $new, $request);

        return back()->with('success', 'Auto backup settings saved. New schedule applies on the next scheduler run.');
    }
}