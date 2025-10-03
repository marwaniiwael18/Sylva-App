<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(['tree_planting', 'maintenance', 'awareness'])
            ],
            'amount' => [
                'required',
                'numeric',
                'min:5',
                'max:50000',
                'decimal:0,2'
            ],
            'custom_amount' => [
                'nullable',
                'numeric',
                'min:5',
                'max:50000',
                'decimal:0,2'
            ],
            'event_id' => [
                'nullable',
                'integer',
                'exists:events,id' // Now enabled
            ],
            'message' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'anonymous' => [
                'nullable',
                'boolean'
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
            'type.required' => 'Please select a donation type.',
            'type.in' => 'Invalid donation type selected.',
            'amount.required' => 'Please specify a donation amount.',
            'amount.min' => 'Minimum donation amount is 5 TND.',
            'amount.max' => 'Maximum donation amount is 50,000 TND.',
            'amount.decimal' => 'Amount must be a valid monetary value with up to 2 decimal places.',
            'custom_amount.min' => 'Minimum custom donation amount is 5 TND.',
            'custom_amount.max' => 'Maximum custom donation amount is 50,000 TND.',
            'custom_amount.decimal' => 'Custom amount must be a valid monetary value with up to 2 decimal places.',
            'event_id.exists' => 'Selected event does not exist.',
            'message.max' => 'Message cannot exceed 1000 characters.'
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
            'type' => 'donation type',
            'amount' => 'donation amount',
            'custom_amount' => 'custom amount',
            'event_id' => 'event',
            'message' => 'message',
            'anonymous' => 'anonymous setting'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If custom amount is provided, use it as the main amount
        if ($this->filled('custom_amount')) {
            $this->merge([
                'amount' => $this->custom_amount
            ]);
        }

        // Convert anonymous checkbox value
        if ($this->has('anonymous')) {
            $this->merge([
                'anonymous' => $this->boolean('anonymous')
            ]);
        }

        // Ensure amount is numeric
        if ($this->filled('amount')) {
            $this->merge([
                'amount' => (float) $this->amount
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
            // Additional validation logic can be added here
            
            // Validate that if event_id is provided, the event is active
            if ($this->filled('event_id')) {
                $event = \App\Models\Event::find($this->event_id);
                if ($event && $event->status !== 'active') {
                    $validator->errors()->add('event_id', 'Selected event is not currently accepting donations.');
                }
            }

            // Validate amount based on donation type (optional business logic)
            $amount = $this->input('amount');
            $type = $this->input('type');
            
            if ($amount && $type) {
                $minimumAmounts = [
                    'tree_planting' => 10,
                    'maintenance' => 5,
                    'awareness' => 5
                ];
                
                if (isset($minimumAmounts[$type]) && $amount < $minimumAmounts[$type]) {
                    $validator->errors()->add('amount', "Minimum amount for {$type} donations is {$minimumAmounts[$type]} TND.");
                }
            }
        });
    }
}