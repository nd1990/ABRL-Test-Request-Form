<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $stats = [
            'total_quotations' => Quotation::count(),
            'month_quotations' => Quotation::whereDate('quotation_date', '>=', $monthStart)->count(),
            'today_quotations' => Quotation::whereDate('quotation_date', $today)->count(),
            'total_value' => Quotation::where('status', '!=', 'cancelled')->sum('grand_total'),
            'sent_emails' => Quotation::where('email_status', 'sent')->count(),
            'failed_emails' => Quotation::where('email_status', 'failed')->count(),
            'pending_emails' => Quotation::where('email_status', 'pending')->count(),
        ];

        $monthly = Quotation::select(
            DB::raw('DATE_FORMAT(quotation_date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(grand_total) as value')
        )
            ->where('quotation_date', '>=', now()->subMonths(11)->startOfMonth()->toDateString())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $popularServices = DB::table('quotation_items')
            ->select('service_name_snapshot', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as qty'))
            ->groupBy('service_name_snapshot')
            ->orderByDesc('count')
            ->limit(6)
            ->get();

        $statusCounts = Quotation::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        $recent = Quotation::with('items')->latest()->limit(8)->get();

        $activeServices = Service::active()->count();

        return view('admin.dashboard', compact('stats', 'monthly', 'popularServices', 'statusCounts', 'recent', 'activeServices'));
    }
}