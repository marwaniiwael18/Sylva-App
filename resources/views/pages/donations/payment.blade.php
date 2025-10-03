@extends('layouts.dashboard')

@section('title', 'Complete Payment - Sylva')
@section('page-title', 'Complete Your Donation')
@section('page-subtitle', 'Secure payment powered by Stripe')

@section('page-content')
<div class="p-6 max-w-2xl mx-auto">
    
    <!-- Back Button -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('donations.show', $donation) }}" 
           class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Donation
        </a>
    </div>

    <!-- Donation Summary -->
    <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200 mb-6">
        <h2 class="text-lg font-semibold text-emerald-900 mb-4">Donation Summary</h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-emerald-700">Type:</span>
                <span class="font-medium text-emerald-900">{{ $donation->type_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-emerald-700">Amount:</span>
                <span class="font-medium text-emerald-900">{{ $donation->formatted_amount }}</span>
            </div>
            @if($donation->relatedEvent)
                <div class="flex justify-between">
                    <span class="text-emerald-700">Event:</span>
                    <span class="font-medium text-emerald-900">{{ $donation->relatedEvent->title }}</span>
                </div>
            @endif
            <div class="border-t border-emerald-200 pt-3 mt-4">
                <div class="flex justify-between text-lg">
                    <span class="font-semibold text-emerald-900">Total:</span>
                    <span class="font-bold text-emerald-900">{{ $donation->formatted_amount }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Payment Information</h2>
        
        <form id="payment-form">
            <!-- Payment Element -->
            <div id="payment-element" class="mb-6">
                <!-- Stripe Elements will create form elements here -->
            </div>

            <!-- Error Message -->
            <div id="payment-message" class="hidden mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
                <div class="flex">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5"></i>
                    <div class="text-red-700" id="payment-message-text"></div>
                </div>
            </div>

            <!-- Payment Button -->
            <button id="submit-button" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                <span id="button-text">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    Complete Payment
                </span>
                <div id="spinner" class="hidden">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                </div>
            </button>
        </form>

        <!-- Security Notice -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <i data-lucide="shield-check" class="w-5 h-5 text-green-500"></i>
                <span>Your payment is secured with 256-bit SSL encryption</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-600 mt-2">
                <i data-lucide="lock" class="w-5 h-5 text-green-500"></i>
                <span>Powered by Stripe - We don't store your card details</span>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">We accept all major payment methods</p>
        <div class="flex justify-center items-center gap-4 opacity-60">
            <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                <span class="text-xs font-bold">VISA</span>
            </div>
            <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                <span class="text-xs font-bold">MC</span>
            </div>
            <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                <span class="text-xs font-bold">AMEX</span>
            </div>
            <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                <span class="text-xs font-bold">DISC</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Payment page loaded');
    
    // Initialize Stripe
    const stripe = Stripe('{{ config("services.stripe.publishable_key") }}');
    console.log('Stripe initialized');
    
    let elements;
    
    // Check if we have a client secret
    @if(isset($clientSecret) && $clientSecret)
        console.log('Client secret available, initializing...');
        initialize('{{ $clientSecret }}');
    @else
        console.log('No client secret available');
        showMessage('Failed to initialize payment. Please refresh and try again.');
    @endif

    async function initialize(clientSecret) {
        console.log('Initializing Stripe Elements with client secret');
        
        elements = stripe.elements({
            clientSecret: clientSecret,
            appearance: {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#059669', // emerald-600
                    colorBackground: '#ffffff',
                    colorText: '#374151',
                    colorDanger: '#dc2626',
                    fontFamily: 'system-ui, sans-serif',
                    spacingUnit: '4px',
                    borderRadius: '8px'
                }
            }
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');
        
        console.log('Payment element mounted');

        // Handle form submission
        const form = document.getElementById('payment-form');
        if (form) {
            form.addEventListener('submit', handleSubmit);
            console.log('Form event listener attached');
        } else {
            console.error('Payment form not found');
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        console.log('Form submitted');
        setLoading(true);

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: window.location.origin + '/donations/{{ $donation->id }}/payment-success',
            },
        });

        if (error) {
            console.error('Payment error:', error);
            if (error.type === "card_error" || error.type === "validation_error") {
                showMessage(error.message);
            } else {
                showMessage("An unexpected error occurred.");
            }
        }

        setLoading(false);
    }

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");
        const messageTextElement = document.querySelector("#payment-message-text");
        
        messageTextElement.textContent = messageText;
        messageContainer.classList.remove("hidden");
        
        // Hide message after 5 seconds
        setTimeout(() => {
            messageContainer.classList.add("hidden");
        }, 5000);
    }

    function setLoading(isLoading) {
        const submitButton = document.querySelector("#submit-button");
        const buttonText = document.querySelector("#button-text");
        const spinner = document.querySelector("#spinner");
        
        if (isLoading) {
            submitButton.disabled = true;
            buttonText.classList.add("hidden");
            spinner.classList.remove("hidden");
        } else {
            submitButton.disabled = false;
            buttonText.classList.remove("hidden");
            spinner.classList.add("hidden");
        }
    }
});
</script>
@endpush
@endsection