@extends('layouts.dashboard')

@section('title', 'Donation Details - Sylva')
@section('page-title', 'Donation #' . $donation->id)
@section('page-subtitle', 'View donation details and transaction information')

@section('page-content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    
    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your request:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Display success messages -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Display error messages -->
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Back Button -->
    <div class="flex items-center gap-4">
        <a href="{{ route('donations.index') }}" 
           class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Donations
        </a>
    </div>

    <!-- Donation Header -->
    <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $donation->formatted_amount }}</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium
                        @if($donation->payment_status === 'succeeded') bg-green-100 text-green-800
                        @elseif($donation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($donation->payment_status === 'processing') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $donation->payment_status_name }}
                    </span>
                </div>
                <p class="text-gray-600">Donated on {{ $donation->created_at->format('F d, Y \a\t H:i') }}</p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                @if($donation->payment_status === 'pending')
                    <a href="{{ route('donations.payment', $donation) }}" 
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        Complete Payment
                    </a>
                @endif
                
                @if($donation->canRefund())
                    <button onclick="openRefundModal()" 
                            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i data-lucide="undo-2" class="w-4 h-4"></i>
                        Request Refund
                    </button>
                @endif
                
                @if($donation->payment_status === 'pending')
                    <button onclick="openCancelModal()" 
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Cancel Donation
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Donation Information -->
            <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Donation Information</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center
                            @if($donation->type === 'tree_planting') bg-green-100
                            @elseif($donation->type === 'maintenance') bg-blue-100
                            @else bg-purple-100 @endif">
                            @if($donation->type === 'tree_planting')
                                <i data-lucide="tree-pine" class="w-6 h-6 text-green-600"></i>
                            @elseif($donation->type === 'maintenance')
                                <i data-lucide="wrench" class="w-6 h-6 text-blue-600"></i>
                            @else
                                <i data-lucide="megaphone" class="w-6 h-6 text-purple-600"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $donation->type_name }}</h3>
                            <p class="text-sm text-gray-600">
                                @if($donation->type === 'tree_planting')
                                    Supporting new tree planting initiatives
                                @elseif($donation->type === 'maintenance')
                                    Funding forest maintenance and care
                                @else
                                    Contributing to environmental awareness campaigns
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($donation->event)
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Linked to Event</h4>
                            <div class="bg-emerald-50 rounded-lg p-4">
                                <h5 class="font-semibold text-emerald-900">{{ $donation->event->title }}</h5>
                                <p class="text-sm text-emerald-700 mt-1">{{ $donation->event->description }}</p>
                                <p class="text-sm text-emerald-600 mt-2">
                                    <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                    {{ $donation->event->date->format('F d, Y') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($donation->message)
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Your Message</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $donation->message }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-semibold text-gray-900">{{ $donation->formatted_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Currency:</span>
                        <span class="font-semibold text-gray-900">{{ strtoupper($donation->currency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Status:</span>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium
                            @if($donation->payment_status === 'succeeded') bg-green-100 text-green-800
                            @elseif($donation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($donation->payment_status === 'processing') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $donation->payment_status_name }}
                        </span>
                    </div>
                    
                    @if($donation->stripe_payment_intent_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment ID:</span>
                            <span class="font-mono text-sm text-gray-900">{{ $donation->stripe_payment_intent_id }}</span>
                        </div>
                    @endif
                    
                    @if($donation->payment_status === 'succeeded' && $donation->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Paid At:</span>
                            <span class="font-semibold text-gray-900">{{ $donation->paid_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Refund Information -->
            @if($donation->refunds->count() > 0)
                <div class="bg-white rounded-2xl p-6 border border-orange-200 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Refund Information</h2>
                    <div class="space-y-3">
                        @foreach($donation->refunds as $refund)
                            <div class="border-b border-gray-100 pb-3 mb-3 last:border-b-0 last:pb-0 last:mb-0">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Refund Status:</span>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium
                                        @if($refund->status === 'completed') bg-green-100 text-green-800
                                        @elseif($refund->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($refund->status === 'processing') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $refund->status_name }}
                                    </span>
                                </div>

                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Refund Amount:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($refund->amount, 2) }} {{ strtoupper($refund->currency) }}</span>
                                </div>

                                @if($refund->reason)
                                    <div class="mb-2">
                                        <h4 class="font-medium text-gray-900 mb-1">Refund Reason</h4>
                                        <div class="bg-orange-50 rounded-lg p-3">
                                            <p class="text-orange-800 text-sm">{{ $refund->reason }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($refund->processed_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Processed At:</span>
                                        <span class="font-semibold text-gray-900">{{ $refund->processed_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Donor Information -->
            <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Donor Information</h3>
                <div class="space-y-3">
                    @if($donation->anonymous)
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="user-x" class="w-6 h-6 text-gray-400"></i>
                            </div>
                            <p class="text-gray-600">Anonymous Donation</p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="text-xl font-bold text-emerald-600">
                                    {{ strtoupper(substr($donation->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <h4 class="font-semibold text-gray-900">{{ $donation->user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $donation->user->email }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="plus" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Donation Created</p>
                            <p class="text-sm text-gray-600">{{ $donation->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($donation->paid_at)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Payment Completed</p>
                                <p class="text-sm text-gray-600">{{ $donation->paid_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($donation->refunded_at)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="undo-2" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Refund Processed</p>
                                <p class="text-sm text-gray-600">{{ $donation->refunded_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200">
                <h3 class="text-lg font-semibold text-emerald-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('donations.create') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Make Another Donation
                    </a>
                    <a href="{{ route('donations.index') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        View All Donations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Refund</h3>
        <form id="refundForm" method="POST" action="{{ route('donations.refund', $donation) }}">
            @csrf
            <div class="mb-4">
                <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for refund <span class="text-red-500">*</span></label>
                <textarea id="refund_reason" name="refund_reason" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('refund_reason') border-red-500 @enderror"
                          placeholder="Please explain why you want to refund this donation (minimum 10 characters)..." required>{{ old('refund_reason') }}</textarea>
                @error('refund_reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRefundModal()" 
                        class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                    Request Refund
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Donation</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to cancel this donation? This action cannot be undone.</p>
        <form method="POST" action="{{ route('donations.cancel', $donation) }}">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" 
                        class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Keep Donation
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    Cancel Donation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@if($errors->has('refund_reason'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    openRefundModal();
});
</script>
@endif

<script>
function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
    document.getElementById('refundModal').classList.add('flex');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundModal').classList.remove('flex');
    document.getElementById('refund_reason').value = '';
}

function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}

// Close modals when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) closeRefundModal();
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endpush
@endsection