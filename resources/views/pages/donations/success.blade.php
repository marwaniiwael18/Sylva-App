@extends('layouts.dashboard')

@section('title', 'Payment Successful - Sylva')
@section('page-title', 'Thank You!')
@section('page-subtitle', 'Your donation has been processed successfully')

@section('page-content')
<div class="p-6 max-w-2xl mx-auto text-center">
    
    <!-- Success Icon -->
    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i data-lucide="check-circle" class="w-12 h-12 text-green-600"></i>
    </div>

    <!-- Success Message -->
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
    <p class="text-xl text-gray-600 mb-8">Thank you for your generous contribution to environmental conservation.</p>

    <!-- Donation Details -->
    <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200 mb-8">
        <h2 class="text-lg font-semibold text-emerald-900 mb-4">Donation Details</h2>
        <div class="space-y-3 text-left">
            <div class="flex justify-between">
                <span class="text-emerald-700">Donation ID:</span>
                <span class="font-medium text-emerald-900">#{{ $donation->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-emerald-700">Amount:</span>
                <span class="font-medium text-emerald-900">{{ $donation->formatted_amount }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-emerald-700">Type:</span>
                <span class="font-medium text-emerald-900">{{ $donation->type_name }}</span>
            </div>
            @if($donation->event)
                <div class="flex justify-between">
                    <span class="text-emerald-700">Event:</span>
                    <span class="font-medium text-emerald-900">{{ $donation->event->title }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-emerald-700">Date:</span>
                <span class="font-medium text-emerald-900">{{ $donation->created_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Impact Message -->
    <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Impact</h3>
        <div class="text-gray-600">
            @if($donation->type === 'tree_planting')
                <p class="mb-4">🌱 Your donation will help us plant new trees and expand forest coverage in our conservation areas.</p>
                <p>With your contribution of {{ $donation->formatted_amount }}, we can make a real difference in combating climate change and preserving biodiversity.</p>
            @elseif($donation->type === 'maintenance')
                <p class="mb-4">🌳 Your donation supports the ongoing care and maintenance of existing forests and green spaces.</p>
                <p>Your contribution of {{ $donation->formatted_amount }} helps ensure our forests remain healthy and continue to thrive for future generations.</p>
            @else
                <p class="mb-4">📢 Your donation funds environmental awareness campaigns and educational programs.</p>
                <p>With your contribution of {{ $donation->formatted_amount }}, we can reach more people and spread the message of environmental conservation.</p>
            @endif
        </div>
    </div>

    <!-- What's Next -->
    <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">What Happens Next?</h3>
        <div class="text-left space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-emerald-600">1</span>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Email Confirmation</h4>
                    <p class="text-sm text-gray-600">You'll receive a confirmation email with your donation receipt shortly.</p>
                </div>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-emerald-600">2</span>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Allocation Process</h4>
                    <p class="text-sm text-gray-600">Your donation will be allocated to the selected environmental cause within 48 hours.</p>
                </div>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-emerald-600">3</span>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Progress Updates</h4>
                    <p class="text-sm text-gray-600">We'll keep you updated on how your donation is making an impact through regular reports.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('donations.show', $donation) }}" 
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
            <i data-lucide="eye" class="w-5 h-5"></i>
            View Donation Details
        </a>
        
        <a href="{{ route('donations.index') }}" 
           class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-emerald-700 border border-emerald-200 px-6 py-3 rounded-xl font-semibold transition-colors">
            <i data-lucide="list" class="w-5 h-5"></i>
            View All Donations
        </a>
        
        <a href="{{ route('donations.create') }}" 
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Make Another Donation
        </a>
    </div>

    <!-- Social Sharing -->
    <div class="mt-8 pt-8 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Your Impact</h3>
        <p class="text-gray-600 mb-4">Help spread awareness about environmental conservation by sharing your contribution!</p>
        
        <div class="flex justify-center gap-3">
            <button onclick="shareOnTwitter()" 
                    class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i data-lucide="twitter" class="w-4 h-4"></i>
                Share on Twitter
            </button>
            
            <button onclick="shareOnFacebook()" 
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i data-lucide="facebook" class="w-4 h-4"></i>
                Share on Facebook
            </button>
            
            <button onclick="copyLink()" 
                    class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i data-lucide="copy" class="w-4 h-4"></i>
                Copy Link
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function shareOnTwitter() {
    const text = `I just made a donation to support environmental conservation through Sylva! 🌱 Every contribution counts in our fight against climate change. #EnvironmentalConservation #ClimateAction #Sylva`;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(window.location.origin)}`;
    window.open(url, '_blank', 'width=600,height=400');
}

function shareOnFacebook() {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.origin)}`;
    window.open(url, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.origin).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Auto-redirect after 30 seconds if user doesn't take action
setTimeout(() => {
    if (confirm('Would you like to view your donation details?')) {
        window.location.href = '{{ route("donations.show", $donation) }}';
    }
}, 30000);
</script>
@endpush
@endsection