<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class DonationController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Get user's donations
     */
    public function index(Request $request)
    {
        $donations = Auth::user()->donations()
            ->with('relatedEvent')
            ->when($request->status, function ($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'donations' => $donations,
            'stats' => $this->getUserDonationStats()
        ]);
    }

    /**
     * Create donation and payment intent
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:5|max:50000',
            'type' => 'required|in:' . implode(',', array_keys(Donation::TYPES)),
            'event_id' => 'nullable|exists:events,id',
            'message' => 'nullable|string|max:1000',
            'anonymous' => 'nullable|boolean'
        ]);

        try {
            // Create donation record
            $donation = Donation::create([
                'amount' => $validated['amount'],
                'currency' => 'TND',
                'type' => $validated['type'],
                'user_id' => Auth::id(),
                'event_id' => $validated['event_id'] ?? null,
                'message' => $validated['message'] ?? null,
                'anonymous' => $validated['anonymous'] ?? false,
                'payment_status' => 'pending'
            ]);

            // Create Stripe PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100, // Convert to cents
                'currency' => 'tnd',
                'metadata' => [
                    'donation_id' => $donation->id,
                    'user_id' => Auth::id(),
                    'type' => $validated['type']
                ]
            ]);

            // Update donation with Stripe PaymentIntent ID
            $donation->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'processing'
            ]);

            return response()->json([
                'success' => true,
                'donation' => $donation,
                'client_secret' => $paymentIntent->client_secret
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating donation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status (webhook)
     */
    public function updatePaymentStatus(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'status' => 'required|in:succeeded,failed,canceled'
        ]);

        $donation = Donation::where('stripe_payment_intent_id', $validated['payment_intent_id'])->first();

        if (!$donation) {
            return response()->json(['success' => false, 'message' => 'Donation not found'], 404);
        }

        $donation->update(['payment_status' => $validated['status']]);

        return response()->json(['success' => true, 'donation' => $donation]);
    }

    /**
     * Request refund
     */
    public function requestRefund(Request $request, Donation $donation)
    {
        // Check ownership
        if ($donation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$donation->canRefund()) {
            return response()->json(['success' => false, 'message' => 'Refund not allowed'], 400);
        }

        $validated = $request->validate([
            'refund_reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:1|max:' . $donation->getTotalRefundableAmount()
        ]);

        $refundAmount = $validated['amount'] ?? $donation->amount;

        try {
            // Process refund with Stripe
            $refund = Refund::create([
                'payment_intent' => $donation->stripe_payment_intent_id,
                'amount' => $refundAmount * 100, // Convert to cents
                'reason' => 'requested_by_customer'
            ]);

            // Update donation
            $donation->update([
                'refund_status' => 'processing',
                'refund_amount' => $refundAmount,
                'refund_reason' => $validated['refund_reason']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_id' => $refund->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get donation statistics
     */
    public function statistics()
    {
        return response()->json([
            'success' => true,
            'stats' => $this->getUserDonationStats()
        ]);
    }

    /**
     * Get events for donation
     */
    public function getEvents()
    {
        $events = Event::where('status', 'active')
            ->where('date', '>=', now())
            ->orderBy('date')
            ->get(['id', 'title', 'date', 'location']);

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    /**
     * Get user donation statistics
     */
    private function getUserDonationStats()
    {
        $user = Auth::user();
        
        return [
            'total_donated' => $user->donations()->successful()->sum('amount'),
            'total_donations' => $user->donations()->successful()->count(),
            'pending_donations' => $user->donations()->where('payment_status', 'pending')->count(),
            'this_month' => $user->donations()
                ->successful()
                ->whereMonth('created_at', date('m'))
                ->sum('amount'),
            'by_type' => $user->donations()
                ->successful()
                ->selectRaw('type, SUM(amount) as total')
                ->groupBy('type')
                ->pluck('total', 'type')
        ];
    }
}