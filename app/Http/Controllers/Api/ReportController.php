<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // GET /reports/summary?period=today|week|month
    public function summary(Request $request)
    {
        [$from, $to] = $this->dateRange($request->period ?? 'today');

        $totalRevenue = Transaction::whereBetween('created_at', [$from, $to])
            ->where('status', 'success')->sum('total_amount');

        $totalTransactions = Transaction::whereBetween('created_at', [$from, $to])
            ->where('status', 'success')->count();

        $topProducts = DB::table('transaction_items as ti')
            ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
            ->join('products as p', 'p.id', '=', 'ti.product_id')
            ->whereBetween('t.created_at', [$from, $to])
            ->where('t.status', 'success')
            ->select('p.name', DB::raw('SUM(ti.quantity) as total_sold'), DB::raw('SUM(ti.subtotal) as revenue'))
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('total_sold')
            ->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period'             => $request->period ?? 'today',
                'total_revenue'      => (float) $totalRevenue,
                'total_transactions' => $totalTransactions,
                'top_products'       => $topProducts,
            ]
        ]);
    }

    // GET /reports/chart?period=week|month
    public function chart(Request $request)
    {
        [$from, $to] = $this->dateRange($request->period ?? 'week');

        $data = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')->orderBy('date')->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function dateRange(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week'  => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }
}