<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogService
{
    public function log($admin, string $action, ?string $entity = null, $entityId = null, $oldData = null, $newData = null, ?Request $request = null): void
    {
        AuditLog::create([
            'admin_id' => $admin?->id,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public function login($admin, ?Request $request = null): void
    {
        $this->log($admin, 'login', 'Admin', $admin->id, null, ['email' => $admin->email], $request);
    }

    public function logout($admin, ?Request $request = null): void
    {
        $this->log($admin, 'logout', 'Admin', $admin->id, null, ['email' => $admin->email], $request);
    }

    public function serviceCreated($admin, $service, ?Request $request = null): void
    {
        $this->log($admin, 'service.created', 'Service', $service->id, null, $service->toArray(), $request);
    }

    public function serviceUpdated($admin, $service, $old, ?Request $request = null): void
    {
        $this->log($admin, 'service.updated', 'Service', $service->id, $old, $service->toArray(), $request);
    }

    public function serviceDeleted($admin, $service, ?Request $request = null): void
    {
        $this->log($admin, 'service.deleted', 'Service', $service->id, $service->toArray(), null, $request);
    }

    public function quotationCreated($admin, $quotation, ?Request $request = null): void
    {
        $this->log($admin, 'quotation.created', 'Quotation', $quotation->id, null, [
            'quotation_number' => $quotation->quotation_number,
            'grand_total' => $quotation->grand_total,
        ], $request);
    }

    public function quotationUpdated($admin, $quotation, $old, ?Request $request = null): void
    {
        $this->log($admin, 'quotation.updated', 'Quotation', $quotation->id, $old, [
            'quotation_number' => $quotation->quotation_number,
            'grand_total' => $quotation->grand_total,
        ], $request);
    }

    public function pdfGenerated($admin, $quotation, ?Request $request = null): void
    {
        $this->log($admin, 'pdf.generated', 'Quotation', $quotation->id, null, [
            'quotation_number' => $quotation->quotation_number,
        ], $request);
    }

    public function emailSent($admin, $quotation, ?Request $request = null): void
    {
        $this->log($admin, 'email.sent', 'Quotation', $quotation->id, null, [
            'quotation_number' => $quotation->quotation_number,
            'to' => $quotation->email,
        ], $request);
    }

    public function emailResent($admin, $quotation, ?Request $request = null): void
    {
        $this->log($admin, 'email.resent', 'Quotation', $quotation->id, null, [
            'quotation_number' => $quotation->quotation_number,
            'to' => $quotation->email,
        ], $request);
    }

    public function quotationDeleted($admin, $quotation, ?Request $request = null): void
    {
        $this->log($admin, 'quotation.deleted', 'Quotation', $quotation->id, [
            'quotation_number' => $quotation->quotation_number,
            'grand_total' => $quotation->grand_total,
        ], null, $request);
    }

    public function settingsChanged($admin, string $group, $old, $new, ?Request $request = null): void
    {
        $this->log($admin, 'settings.updated', 'Settings', null, $old, $new, $request);
    }

    public function statusChanged($admin, $quotation, $oldStatus, $newStatus, ?Request $request = null): void
    {
        $this->log($admin, 'quotation.status_changed', 'Quotation', $quotation->id, [
            'status' => $oldStatus,
        ], [
            'status' => $newStatus,
        ], $request);
    }
}
