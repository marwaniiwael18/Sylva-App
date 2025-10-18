@extends('layouts.dashboard')

@section('title', 'My Donations - Sylva')
@section('page-title', 'My Donations')
@section('page-subtitle', 'Track your environmental contributions')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3"></i>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3"></i>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
                    <i data-lucide="coins" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Donated</p>
                    <p class="text-2xl font-bold text-emerald-900">{{ number_format($stats['total_donated'], 2) }} EUR</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Donations</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_donations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_donations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($stats['this_month'], 2) }} EUR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Donation History</h2>
            <p class="text-sm text-gray-600">Manage your contributions to environmental causes</p>
        </div>
        <a href="{{ route('donations.create') }}" 
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Make New Donation
        </a>
    </div>

    <!-- Donations List -->
    <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
        @if($donations->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-emerald-50 border-b border-emerald-200">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Amount</th>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Type</th>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Event</th>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Status</th>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Date</th>
                            <th class="text-left py-4 px-6 font-semibold text-emerald-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100">
                        @foreach($donations as $donation)
                            <tr class="hover:bg-emerald-25 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-gray-900">{{ $donation->formatted_amount }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium
                                        @if($donation->type === 'tree_planting') bg-green-100 text-green-800
                                        @elseif($donation->type === 'maintenance') bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        @if($donation->type === 'tree_planting')
                                            <i data-lucide="tree-pine" class="w-3 h-3"></i>
                                        @elseif($donation->type === 'maintenance')
                                            <i data-lucide="wrench" class="w-3 h-3"></i>
                                        @else
                                            <i data-lucide="megaphone" class="w-3 h-3"></i>
                                        @endif
                                        {{ $donation->type_name }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($donation->event)
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900">{{ $donation->event->title }}</div>
                                            <div class="text-gray-500">{{ $donation->event->date->format('M d, Y') }}</div>
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-sm">General Donation</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium
                                        @if($donation->payment_status === 'succeeded') bg-green-100 text-green-800
                                        @elseif($donation->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($donation->payment_status === 'processing') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $donation->payment_status_name }}
                                    </span>
                                    @if($donation->refunds->count() > 0)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs
                                                @if($donation->refunds->where('status', 'completed')->count() > 0) bg-gray-100 text-gray-800
                                                @else bg-orange-100 text-orange-800 @endif">
                                                <i data-lucide="undo-2" class="w-3 h-3"></i>
                                                @if($donation->refunds->where('status', 'completed')->count() > 0)
                                                    Refunded
                                                @else
                                                    Refund Pending
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-sm text-gray-900">{{ $donation->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $donation->created_at->format('H:i') }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('donations.show', $donation) }}" 
                                           class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            View
                                        </a>
                                        
                                        @if($donation->payment_status === 'pending')
                                            <a href="{{ route('donations.payment', $donation) }}" 
                                               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                                Pay
                                            </a>
                                        @endif
                                        
                                        @if($donation->canRefund())
                                            <button onclick="openRefundModal({{ $donation->id }})"
                                                    class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 text-sm font-medium">
                                                <i data-lucide="undo-2" class="w-4 h-4"></i>
                                                Refund
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-emerald-200">
                {{ $donations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="heart" class="w-12 h-12 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No donations yet</h3>
                <p class="text-gray-600 mb-6">Start contributing to environmental causes today!</p>
                <a href="{{ route('donations.create') }}" 
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Make Your First Donation
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Refund</h3>
        <form id="refundForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for refund</label>
                <textarea id="refund_reason" name="refund_reason" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                          placeholder="Please explain why you want to refund this donation..." required></textarea>
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

@push('scripts')
<script>
function openRefundModal(donationId) {
    document.getElementById('refundForm').action = `/donations/${donationId}/refund`;
    document.getElementById('refundModal').classList.remove('hidden');
    document.getElementById('refundModal').classList.add('flex');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundModal').classList.remove('flex');
    document.getElementById('refund_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>
@endpush
@endsection