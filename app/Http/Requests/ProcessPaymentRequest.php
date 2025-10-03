<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return false;
        }

        $donation = $this->route('donation');
        
        // User can only process payment for their own donations
        if ($donation && $donation->user_id !== auth()->id()) {
            return false;
        }

        // Check if donation is in pending status
        if ($donation && $donation->payment_status !== 'pending') {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method_id' => [
                'nullable',
                'string'
            ],
            'payment_intent_id' => [
                'nullable',
                'string'
            ],
            'return_url' => [
                'nullable',
                'url'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method_id.string' => 'Invalid payment method provided.',
            'payment_intent_id.string' => 'Invalid payment intent provided.',
            'return_url.url' => 'Return URL must be a valid URL.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'payment_method_id' => 'payment method',
            'payment_intent_id' => 'payment intent',
            'return_url' => 'return URL'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $donation = $this->route('donation');
            
            if ($donation) {
                // Check if donation amount is valid
                if ($donation->amount <= 0) {
                    $validator->errors()->add('amount', 'Invalid donation amount.');
                }

                // Check if donation is not expired (e.g., 24 hours)
                $expirationTime = $donation->created_at->addHours(24);
                if (now() > $expirationTime) {
                    $validator->errors()->add('donation', 'This donation request has expired. Please create a new donation.');
                }

                // Check if payment has already been processed
                if (in_array($donation->payment_status, ['succeeded', 'processing'])) {
                    $validator->errors()->add('payment', 'Payment has already been processed for this donation.');
                }

                // Check if donation has been cancelled
                if ($donation->payment_status === 'cancelled') {
                    $validator->errors()->add('donation', 'This donation has been cancelled.');
                }
            }
        });
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        $donation = $this->route('donation');
        
        if (!auth()->check()) {
            abort(401, 'You must be logged in to process payment.');
        }
        
        if ($donation && $donation->user_id !== auth()->id()) {
            abort(403, 'You can only process payment for your own donations.');
        }
        
        if ($donation && $donation->payment_status !== 'pending') {
            abort(403, 'This donation is not in a payable state.');
        }
        
        abort(403, 'You are not authorized to process payment for this donation.');
    }
}