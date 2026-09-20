<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Donation;
use App\Models\FundingTarget;
use App\Models\Grant;
use Illuminate\Support\Facades\DB;

class FundingController extends Controller
{
    public function index()
    {
        $currentYear = (int) (request('year', now()->year));

        $receivedDonationCents = (int) round(
            Donation::query()
                ->where('status', 'completed')
                ->whereYear('transaction_date', $currentYear)
                ->sum(DB::raw('amount * 100'))
        );

        $receivedGrantCents = (int) round(
            Grant::query()
                ->whereYear('start_date', $currentYear)
                ->sum(DB::raw('total_amount * 100'))
        );

        $receivedTotalCents = $receivedDonationCents + $receivedGrantCents;

        $allocatedTotalCents = (int) Allocation::query()
            ->whereYear('date_allocated', $currentYear)
            ->sum('amount_cents');

        $targetTotalCents = (int) FundingTarget::query()
            ->where('year', $currentYear)
            ->sum('target_amount_cents');

        $monthlyReceived = $this->monthlyReceivedTotals($currentYear);
        $monthlyAllocated = $this->monthlyAllocatedTotals($currentYear);

        return view('funding.index', [
            'currentYear' => $currentYear,
            'receivedTotalCents' => $receivedTotalCents,
            'allocatedTotalCents' => $allocatedTotalCents,
            'targetTotalCents' => $targetTotalCents,
            'remainingTargetCents' => max(0, $targetTotalCents - $receivedTotalCents),
            'receivedProgressPercent' => $targetTotalCents > 0 ? min(100, round(($receivedTotalCents / $targetTotalCents) * 100, 1)) : 0,
            'allocatedVsReceivedPercent' => $receivedTotalCents > 0 ? min(100, round(($allocatedTotalCents / $receivedTotalCents) * 100, 1)) : 0,
            'monthlyLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'monthlyReceived' => $monthlyReceived,
            'monthlyAllocated' => $monthlyAllocated,
        ]);
    }

    private function monthlyReceivedTotals(int $year): array
    {
        $donationTotals = Donation::query()
            ->selectRaw('MONTH(transaction_date) as month, SUM(amount * 100) as total')
            ->where('status', 'completed')
            ->whereYear('transaction_date', $year)
            ->groupByRaw('MONTH(transaction_date)')
            ->pluck('total', 'month')
            ->all();

        $grantTotals = Grant::query()
            ->selectRaw('MONTH(start_date) as month, SUM(total_amount * 100) as total')
            ->whereYear('start_date', $year)
            ->groupByRaw('MONTH(start_date)')
            ->pluck('total', 'month')
            ->all();

        $months = array_fill(1, 12, 0);

        foreach ($donationTotals as $month => $total) {
            $months[(int) $month] += (int) round($total);
        }

        foreach ($grantTotals as $month => $total) {
            $months[(int) $month] += (int) round($total);
        }

        return array_values($months);
    }

    private function monthlyAllocatedTotals(int $year): array
    {
        $totals = Allocation::query()
            ->selectRaw('MONTH(date_allocated) as month, SUM(amount_cents) as total')
            ->whereYear('date_allocated', $year)
            ->groupByRaw('MONTH(date_allocated)')
            ->pluck('total', 'month')
            ->all();

        $months = array_fill(1, 12, 0);

        foreach ($totals as $month => $total) {
            $months[(int) $month] = (int) round($total);
        }

        return array_values($months);
    }
}
