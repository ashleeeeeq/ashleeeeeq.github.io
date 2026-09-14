<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonorDashboardController extends Controller
{
    /**
     * Get donor dashboard data with stats and recent activity
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only donors can access this endpoint.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            // Get donation statistics
            $totalDonated = Donation::where('donor_id', $donor->id)
                ->sum('amount');
            
            $totalDonations = Donation::where('donor_id', $donor->id)
                ->count();
            
            $activeSubscriptions = Subscription::where('donor_id', $donor->id)
                ->where('status', 'active')
                ->count();
            
            // Get last donation date
            $lastDonation = Donation::where('donor_id', $donor->id)
                ->latest('transaction_date')
                ->first();
            
            $lastDonationDate = $lastDonation ? $lastDonation->transaction_date : null;
            
            // Get recent donations (for future use)
            $recentDonations = Donation::with('program')
                ->where('donor_id', $donor->id)
                ->latest('transaction_date')
                ->limit(5)
                ->get()
                ->map(fn (Donation $d) => [
                    'id' => $d->id,
                    'amount' => $d->amount,
                    'currency' => $d->currency,
                    'status' => $d->status,
                    'donation_type' => $d->donation_type,
                    'gateway' => $d->gateway,
                    'program' => $d->program ? ['id' => $d->program->id, 'name' => $d->program->program_name] : null,
                    'transaction_date' => $d->transaction_date?->toIso8601String(),
                ]);
            
            // Calculate donation trends (last 6 months)
            $sixMonthsAgo = Carbon::now()->subMonths(6);
            $monthlyTrends = Donation::where('donor_id', $donor->id)
                ->where('transaction_date', '>=', $sixMonthsAgo)
                ->select(
                    DB::raw('YEAR(transaction_date) as year'),
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->map(fn ($item) => [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'total' => (float) $item->total,
                ]);
            
            // Get active subscription details
            $activeSubscription = Subscription::where('donor_id', $donor->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();
            
            $nextBilling = null;
            if ($activeSubscription && $activeSubscription->next_billing_date) {
                $nextBilling = [
                    'date' => $activeSubscription->next_billing_date->toDateString(),
                    'amount' => $activeSubscription->amount,
                    'plan_name' => $activeSubscription->displayPlanName(),
                ];
            }
            
            // Calculate average donation
            $averageDonation = $totalDonations > 0 ? $totalDonated / $totalDonations : 0;
            
            // Get program distribution
            $programDistribution = Donation::where('donor_id', $donor->id)
                ->whereNotNull('program_id')
                ->with('program')
                ->get()
                ->groupBy('program_id')
                ->map(function ($donations, $programId) {
                    $program = $donations->first()->program;
                    return [
                        'program_name' => $program ? $program->program_name : 'Unknown',
                        'total_amount' => $donations->sum('amount'),
                        'percentage' => 0, // Calculate percentage after total is known
                    ];
                })
                ->values();
            
            // Calculate percentages for program distribution
            $totalAmount = $programDistribution->sum('total_amount');
            $programDistribution = $programDistribution->map(function ($item) use ($totalAmount) {
                $item['percentage'] = $totalAmount > 0 ? round(($item['total_amount'] / $totalAmount) * 100, 1) : 0;
                return $item;
            });
            
            $data = [
                'donor' => [
                    'id' => $donor->id,
                    'display_name' => $donor->display_name,
                    'donor_type' => $donor->donor_type,
                    'email' => $user->email,
                ],
                'stats' => [
                    'total_donated' => (float) $totalDonated,
                    'total_donations' => (int) $totalDonations,
                    'active_subscriptions' => (int) $activeSubscriptions,
                    'average_donation' => (float) $averageDonation,
                    'last_donation_date' => $lastDonationDate ? $lastDonationDate->toDateString() : null,
                    'last_donation_amount' => $lastDonation ? (float) $lastDonation->amount : null,
                ],
                'recent_donations' => $recentDonations,
                'monthly_trends' => $monthlyTrends,
                'program_distribution' => $programDistribution,
                'next_billing' => $nextBilling,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('DonorDashboardController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donor dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get donation summary statistics only
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            $totalDonated = Donation::where('donor_id', $donor->id)
                ->sum('amount');
            
            $totalDonations = Donation::where('donor_id', $donor->id)
                ->count();
            
            $activeSubscriptions = Subscription::where('donor_id', $donor->id)
                ->where('status', 'active')
                ->count();
            
            $lastDonation = Donation::where('donor_id', $donor->id)
                ->latest('transaction_date')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_donated' => (float) $totalDonated,
                    'total_donations' => (int) $totalDonations,
                    'active_subscriptions' => (int) $activeSubscriptions,
                    'last_donation_date' => $lastDonation ? $lastDonation->transaction_date?->toDateString() : null,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donation summary: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get donation trends over time
     */
    public function trends(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            $months = (int) $request->query('months', 12);
            $startDate = Carbon::now()->subMonths($months);
            
            $trends = Donation::where('donor_id', $donor->id)
                ->where('transaction_date', '>=', $startDate)
                ->select(
                    DB::raw('YEAR(transaction_date) as year'),
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(amount) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->map(fn ($item) => [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'total_amount' => (float) $item->total,
                    'donation_count' => (int) $item->count,
                ]);
            
            return response()->json([
                'success' => true,
                'data' => $trends,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donation trends: ' . $e->getMessage()
            ], 500);
        }
    }
}