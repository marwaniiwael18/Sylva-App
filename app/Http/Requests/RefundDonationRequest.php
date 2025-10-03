<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated and owns the donation
        if (!auth()->check()) {
            return false;
        }

        $donation = $this->route('donation');
        
        // User can only refund their own donations
        if ($donation && $donation->user_id !== auth()->id()) {
            return false;
        }

        // Check if donation is eligible for refund
        if ($donation && !$donation->canRefund()) {
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
            'refund_reason' => [
                'required',
                'string',
                'min:10',
                'max:500'
            ],
            'refund_amount' => [
                'nullable',
                'numeric',
                'min:0.01',
                'decimal:0,2'
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
            'refund_reason.required' => 'Please provide a reason for the refund request.',
            'refund_reason.min' => 'Refund reason must be at least 10 characters long.',
            'refund_reason.max' => 'Refund reason cannot exceed 500 characters.',
            'refund_amount.min' => 'Refund amount must be greater than 0.',
            'refund_amount.decimal' => 'Refund amount must be a valid monetary value with up to 2 decimal places.'
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
            'refund_reason' => 'refund reason',
            'refund_amount' => 'refund amount'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If no refund amount is specified, default to full refund
        $donation = $this->route('donation');
        
        if (!$this->filled('refund_amount') && $donation) {
            $this->merge([
                'refund_amount' => $donation->amount
            ]);
        }

        // Ensure refund amount is numeric
        if ($this->filled('refund_amount')) {
            $this->merge([
                'refund_amount' => (float) $this->refund_amount
            ]);
        }
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
            $refundAmount = $this->input('refund_amount');
            
            if ($donation && $refundAmount) {
                // Check if refund amount doesn't exceed the original donation amount
                if ($refundAmount > $donation->amount) {
                    $validator->errors()->add('refund_amount', 'Refund amount cannot exceed the original donation amount.');
                }

                // Check if there's already a pending refund
                if ($donation->refund_status === 'pending') {
                    $validator->errors()->add('refund_reason', 'There is already a pending refund request for this donation.');
                }

                // Check if donation has already been refunded
                if ($donation->refund_status === 'succeeded') {
                    $validator->errors()->add('refund_reason', 'This donation has already been refunded.');
                }

                // Check refund time limit (e.g., 30 days)
                $refundTimeLimit = now()->subDays(30);
                if ($donation->created_at < $refundTimeLimit) {
                    $validator->errors()->add('refund_reason', 'Refund requests can only be made within 30 days of the original donation.');
                }

                // Check if payment was successful
                if ($donation->payment_status !== 'succeeded') {
                    $validator->errors()->add('refund_reason', 'Only successfully paid donations can be refunded.');
                }
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function failedAuthorization(): void
    {
        $donation = $this->route('donation');
        
        if (!auth()->check()) {
            abort(401, 'You must be logged in to request a refund.');
        }
        
        if ($donation && $donation->user_id !== auth()->id()) {
            abort(403, 'You can only refund your own donations.');
        }
        
        if ($donation && !$donation->canRefund()) {
            abort(403, 'This donation is not eligible for refund.');
        }
        
        abort(403, 'You are not authorized to request a refund for this donation.');
    }
}