@extends('layouts.dashboard')

@section('title', 'Make a Donation - Sylva')
@section('page-title', 'Make a Donation')
@section('page-subtitle', 'Support environmental conservation')

@section('page-content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Messages -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5"></i>
                <div class="text-sm text-green-700">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <i data-lucide="x-circle" class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5"></i>
                <div class="text-sm text-red-700">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <form id="donationForm" method="POST" action="{{ route('donations.store') }}" class="space-y-8">
        @csrf
        
        <!-- Donation Type Selection -->
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Your Impact</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="tree_planting" class="sr-only" required>
                    <div class="donation-type-card p-6 border-2 border-gray-200 rounded-xl hover:border-emerald-300 transition-colors">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="tree-pine" class="w-8 h-8 text-green-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Tree Planting</h4>
                            <p class="text-sm text-gray-600">Help us plant new trees and expand forest coverage</p>
                        </div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="maintenance" class="sr-only">
                    <div class="donation-type-card p-6 border-2 border-gray-200 rounded-xl hover:border-emerald-300 transition-colors">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="wrench" class="w-8 h-8 text-blue-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Maintenance</h4>
                            <p class="text-sm text-gray-600">Support ongoing care and maintenance of existing forests</p>
                        </div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="awareness" class="sr-only">
                    <div class="donation-type-card p-6 border-2 border-gray-200 rounded-xl hover:border-emerald-300 transition-colors">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="megaphone" class="w-8 h-8 text-purple-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Awareness</h4>
                            <p class="text-sm text-gray-600">Fund educational programs and awareness campaigns</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Amount Selection -->
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Your Amount</h3>
            
            <!-- Preset Amounts -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <label class="relative cursor-pointer">
                    <input type="radio" name="amount_preset" value="25" class="sr-only">
                    <div class="amount-card p-4 border-2 border-gray-200 rounded-xl text-center hover:border-emerald-300 transition-colors">
                        <div class="text-lg font-bold text-gray-900">25 EUR</div>
                        <div class="text-xs text-gray-600">Basic Support</div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="amount_preset" value="50" class="sr-only">
                    <div class="amount-card p-4 border-2 border-gray-200 rounded-xl text-center hover:border-emerald-300 transition-colors">
                        <div class="text-lg font-bold text-gray-900">50 EUR</div>
                        <div class="text-xs text-gray-600">Standard</div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="amount_preset" value="100" class="sr-only">
                    <div class="amount-card p-4 border-2 border-gray-200 rounded-xl text-center hover:border-emerald-300 transition-colors">
                        <div class="text-lg font-bold text-gray-900">100 EUR</div>
                        <div class="text-xs text-gray-600">Generous</div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="amount_preset" value="custom" class="sr-only">
                    <div class="amount-card p-4 border-2 border-gray-200 rounded-xl text-center hover:border-emerald-300 transition-colors">
                        <div class="text-lg font-bold text-gray-900">Custom</div>
                        <div class="text-xs text-gray-600">Your Choice</div>
                    </div>
                </label>
            </div>

            <!-- Custom Amount Input -->
            <div id="customAmountSection" class="hidden">
                <label for="custom_amount" class="block text-sm font-medium text-gray-700 mb-2">Custom Amount (EUR)</label>
                <div class="relative">
                    <input type="number" 
                           id="custom_amount" 
                           name="custom_amount" 
                           min="5" 
                           step="0.01"
                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Enter amount">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <span class="text-gray-500 text-sm font-medium">EUR</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">Minimum donation is 5 EUR</p>
            </div>

            <input type="hidden" name="amount" id="final_amount">
        </div>

        <!-- Event Selection (Optional) -->
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Link to Event (Optional)</h3>
            <select name="event_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">General Donation (Not linked to any event)</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->title }} - {{ $event->date->format('M d, Y') }}</option>
                @endforeach
            </select>
            <p class="text-sm text-gray-600 mt-2">You can link your donation to a specific environmental event or keep it as a general contribution.</p>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                    <textarea id="message" 
                              name="message" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Leave a message about your donation..."></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           id="anonymous" 
                           name="anonymous" 
                           value="1"
                           class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <label for="anonymous" class="ml-2 text-sm text-gray-700">Make this donation anonymous</label>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div id="donationSummary" class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200">
            <h3 class="text-lg font-semibold text-emerald-900 mb-4">Donation Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-emerald-700">Type:</span>
                    <span id="summary-type" class="font-medium text-emerald-900">Please select a donation type</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-700">Amount:</span>
                    <span id="summary-amount" class="font-medium text-emerald-900">Please select an amount</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-700">Event:</span>
                    <span id="summary-event" class="font-medium text-emerald-900">General Donation</span>
                </div>
                <div class="border-t border-emerald-200 pt-2 mt-3">
                    <div class="flex justify-between text-base">
                        <span class="font-semibold text-emerald-900">Total:</span>
                        <span id="summary-total" class="font-bold text-emerald-900">0.00 EUR</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('donations.index') }}" 
               class="flex-1 text-center px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    id="submitButton"
                    disabled
                    class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-colors">
                Proceed to Payment
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donationForm');
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const amountInputs = document.querySelectorAll('input[name="amount_preset"]');
    const customAmountInput = document.getElementById('custom_amount');
    const customAmountSection = document.getElementById('customAmountSection');
    const eventSelect = document.querySelector('select[name="event_id"]');
    const finalAmountInput = document.getElementById('final_amount');
    const submitButton = document.getElementById('submitButton');

    // Add form submission debugging
    form.addEventListener('submit', function(e) {
        console.log('Form submission started...');
        
        // Log form data
        const formData = new FormData(form);
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Check required fields
        const type = formData.get('type');
        const amount = formData.get('amount');
        
        console.log('Type:', type);
        console.log('Amount:', amount);
        
        if (!type) {
            console.error('Type not selected');
            e.preventDefault();
            alert('Please select a donation type');
            return;
        }
        
        if (!amount || amount < 5) {
            console.error('Invalid amount:', amount);
            e.preventDefault();
            alert('Please select a valid amount (minimum 5 EUR)');
            return;
        }
        
        console.log('Form validation passed, submitting...');
    });

    // Type names mapping
    const typeNames = {
        'tree_planting': 'Tree Planting',
        'maintenance': 'Forest Maintenance',
        'awareness': 'Awareness Campaign'
    };

    // Update donation type cards
    typeInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Remove active class from all cards
            document.querySelectorAll('.donation-type-card').forEach(card => {
                card.classList.remove('border-emerald-500', 'bg-emerald-50');
                card.classList.add('border-gray-200');
            });
            
            // Add active class to selected card
            if (this.checked) {
                const card = this.parentElement.querySelector('.donation-type-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-emerald-500', 'bg-emerald-50');
            }
            
            updateSummary();
        });
    });

    // Update amount cards
    amountInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Remove active class from all amount cards
            document.querySelectorAll('.amount-card').forEach(card => {
                card.classList.remove('border-emerald-500', 'bg-emerald-50');
                card.classList.add('border-gray-200');
            });
            
            // Add active class to selected card
            if (this.checked) {
                const card = this.parentElement.querySelector('.amount-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-emerald-500', 'bg-emerald-50');
                
                // Show/hide custom amount section
                if (this.value === 'custom') {
                    customAmountSection.classList.remove('hidden');
                    customAmountInput.focus();
                } else {
                    customAmountSection.classList.add('hidden');
                    finalAmountInput.value = this.value;
                }
            }
            
            updateSummary();
        });
    });

    // Handle custom amount input
    customAmountInput.addEventListener('input', function() {
        finalAmountInput.value = this.value;
        updateSummary();
    });

    // Handle event selection
    eventSelect.addEventListener('change', updateSummary);

    function updateSummary() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        const selectedAmount = document.querySelector('input[name="amount_preset"]:checked');
        const customAmount = customAmountInput.value;
        const selectedEvent = eventSelect.selectedOptions[0];

        // Update type
        document.getElementById('summary-type').textContent = 
            selectedType ? typeNames[selectedType.value] : 'Please select a donation type';

        // Update amount
        let amount = 0;
        if (selectedAmount) {
            if (selectedAmount.value === 'custom') {
                amount = parseFloat(customAmount) || 0;
            } else {
                amount = parseFloat(selectedAmount.value);
            }
        }
        
        document.getElementById('summary-amount').textContent = 
            amount > 0 ? `${amount.toFixed(2)} EUR` : 'Please select an amount';
        
        document.getElementById('summary-total').textContent = `${amount.toFixed(2)} EUR`;

        // Update event
        document.getElementById('summary-event').textContent = 
            selectedEvent.value ? selectedEvent.textContent : 'General Donation';

        // Enable/disable submit button
        const isValid = selectedType && amount >= 5;
        submitButton.disabled = !isValid;
    }

    // Initialize summary
    updateSummary();
});
</script>
@endpush
@endsection