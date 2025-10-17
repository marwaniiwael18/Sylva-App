<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TreeMaintenanceRequest extends FormRequest
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
        $rules = [
            'tree_id' => 'required|exists:trees,id',
            'activity_type' => 'required|in:watering,pruning,fertilizing,disease_treatment,inspection,other',
            'performed_at' => 'required|date|before_or_equal:today',
            'condition_after' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
            'event_id' => 'nullable|exists:events,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ];

        // For update requests, make tree_id optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['tree_id'] = 'sometimes|exists:trees,id';
            $rules['activity_type'] = 'sometimes|in:watering,pruning,fertilizing,disease_treatment,inspection,other';
            $rules['performed_at'] = 'sometimes|date|before_or_equal:today';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tree_id.required' => 'Please select a tree for this maintenance activity.',
            'tree_id.exists' => 'The selected tree does not exist.',
            'activity_type.required' => 'Please specify the type of maintenance activity.',
            'activity_type.in' => 'Invalid activity type selected.',
            'performed_at.required' => 'Please specify when the maintenance was performed.',
            'performed_at.date' => 'The performed date must be a valid date.',
            'performed_at.before_or_equal' => 'The performed date cannot be in the future.',
            'condition_after.in' => 'Invalid condition value. Must be: excellent, good, fair, or poor.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'event_id.exists' => 'The selected event does not exist.',
            'images.max' => 'You can upload a maximum of 5 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be in jpeg, png, jpg, gif, or webp format.',
            'images.*.max' => 'Each image must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tree_id' => 'tree',
            'activity_type' => 'activity type',
            'performed_at' => 'performed date',
            'condition_after' => 'condition after maintenance',
            'event_id' => 'event',
        ];
    }
}
