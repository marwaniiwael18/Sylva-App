<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Event; // Now enabled since we have events table
use App\Models\Refund;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\RefundDonationRequest;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class DonationController extends Controller
{
    /**
     * Display a listing of user's donations.
     */
    public function index()
    {
        $donations = Auth::user()->donations()
            ->with(['event', 'refunds']) // Load event and refunds relationships
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_donated' => Auth::user()->donations()->where('payment_status', 'succeeded')->sum('amount'),
            'total_donations' => Auth::user()->donations()->where('payment_status', 'succeeded')->count(),
            'pending_donations' => Auth::user()->donations()->where('payment_status', 'pending')->count(),
            'this_month' => Auth::user()->donations()
                ->where('payment_status', 'succeeded')
                ->whereMonth('created_at', date('m'))
                ->sum('amount')
        ];

        return view('pages.donations.index', compact('donations', 'stats'));
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create()
    {
        // Get active upcoming events
        $events = Event::active()
            ->upcoming()
            ->orderBy('date')
            ->get();
        
        $donationTypes = Donation::TYPES;

        return view('pages.donations.create', compact('events', 'donationTypes'));
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(StoreDonationRequest $request)
    {
        \Log::info('Donation store method called');
        \Log::info('Request data:', $request->all());
        
        $validated = $request->validated();
        \Log::info('Validated data:', $validated);

        // Ensure user is authenticated
        $userId = Auth::id();
        if (!$userId) {
            \Log::error('User not authenticated');
            return redirect()->route('login')->with('error', 'You must be logged in to make a donation.');
        }

        // Prepare donation data
        $donationData = [
            'amount' => $validated['amount'],
            'currency' => 'EUR', // Changed from TND to EUR (supported by Stripe)
            'type' => $validated['type'],
            'user_id' => $userId,
            'event_id' => $validated['event_id'] ?? null,
            'message' => $validated['message'] ?? null,
            'anonymous' => $validated['anonymous'] ?? false,
            'payment_status' => 'pending'
        ];

        \Log::info('Donation data to create:', $donationData);

        // Create donation record
        try {
            $donation = Donation::create($donationData);
            \Log::info('Donation created successfully:', ['id' => $donation->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to create donation:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create donation: ' . $e->getMessage());
        }

        // Redirect to payment page
        \Log::info('Redirecting to payment page');
        return redirect()->route('donations.payment', $donation)
            ->with('success', 'Donation created! Please complete your payment.');
    }

    /**
     * Display the specified donation.
     */
    public function show(Donation $donation)
    {
        // Check if user owns this donation
        if ($donation->user_id !== Auth::id()) {
            abort(403, 'You can only view your own donations.');
        }

        // Load relationships
        $donation->load(['event', 'refunds']);

        return view('pages.donations.show', compact('donation'));
    }

    /**
     * Show payment page for donation
     */
    public function payment(Donation $donation)
    {
        // Check if user owns this donation
        if ($donation->user_id !== Auth::id()) {
            abort(403, 'You can only access payment for your own donations.');
        }

        // Check if already paid
        if ($donation->payment_status === 'succeeded') {
            return redirect()->route('donations.show', $donation)
                ->with('info', 'This donation has already been paid.');
        }

        $clientSecret = null;

        try {
            // Create or retrieve PaymentIntent
            if (!$donation->stripe_payment_intent_id) {
                $paymentIntent = $donation->createPaymentIntent();
                $clientSecret = $paymentIntent->client_secret;
            } else {
                // Retrieve existing PaymentIntent
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $paymentIntent = $stripe->paymentIntents->retrieve($donation->stripe_payment_intent_id);
                $clientSecret = $paymentIntent->client_secret;
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initialize payment: ' . $e->getMessage());
        }

        return view('pages.donations.payment', compact('donation', 'clientSecret'));
    }

    /**
     * Process refund request
     */
    public function refund(RefundDonationRequest $request, Donation $donation)
    {
        // Debug logging
        Log::info('Refund method called', [
            'donation_id' => $donation->id,
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        // Validation and authorization are handled by RefundDonationRequest
        $validated = $request->validated();

        Log::info('Validation passed', ['validated_data' => $validated]);

        try {
            // Create refund record in the refunds table
            $refund = Refund::create([
                'donation_id' => $donation->id,
                'processed_by' => null, // User-initiated, not processed by admin yet
                'amount' => $validated['refund_amount'] ?? $donation->amount, // Default to full amount if not specified
                'currency' => $donation->currency,
                'status' => 'pending',
                'reason' => $validated['refund_reason'],
            ]);

            Log::info('Refund request processed successfully', ['donation_id' => $donation->id, 'refund_id' => $refund->id]);

            return back()->with('success', 'Refund request submitted successfully. We will process it within 3-5 business days.');
        } catch (\Exception $e) {
            Log::error('Refund request failed', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Cancel donation (only if pending)
     */
    public function cancel(Donation $donation)
    {
        // Check if user owns this donation
        if ($donation->user_id !== Auth::id()) {
            abort(403, 'You can only cancel your own donations.');
        }

        // Can only cancel pending donations
        if ($donation->payment_status !== 'pending') {
            return back()->with('error', 'This donation cannot be cancelled.');
        }

        $donation->update(['payment_status' => 'canceled']);

        return redirect()->route('donations.index')
            ->with('success', 'Donation cancelled successfully.');
    }

    /**
     * Handle successful payment from Stripe
     */
    public function paymentSuccess(Request $request, Donation $donation)
    {
        // Check if user owns this donation
        if ($donation->user_id !== Auth::id()) {
            abort(403, 'You can only access your own donations.');
        }

        // Get payment intent ID from the URL parameters
        $paymentIntentId = $request->get('payment_intent');
        
        if ($paymentIntentId && $donation->stripe_payment_intent_id === $paymentIntentId) {
            try {
                // Verify payment with Stripe
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);
                
                if ($paymentIntent->status === 'succeeded') {
                    // Update donation status
                    $donation->update([
                        'payment_status' => 'succeeded',
                        'stripe_charge_id' => $paymentIntent->latest_charge,
                        'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                        'paid_at' => now(),
                    ]);

                    return view('pages.donations.success', compact('donation'))
                        ->with('success', 'Payment completed successfully! Thank you for your donation.');
                }
            } catch (\Exception $e) {
                return redirect()->route('donations.show', $donation)
                    ->with('error', 'Failed to verify payment: ' . $e->getMessage());
            }
        }

        return redirect()->route('donations.show', $donation)
            ->with('error', 'Payment verification failed.');
    }
}