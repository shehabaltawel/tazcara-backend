<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Booking Request
 */
class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Merge the Idempotency-Key header into the validated input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => $this->header('Idempotency-Key'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'seats' => ['required', 'array', 'min:1', 'max:12'],
            'seats.*' => ['required', 'string', 'distinct', Rule::exists('seats', 'uuid')->whereNull('deleted_at')],
            'from_city' => ['required', 'string', Rule::exists('cities', 'uuid')->whereNull('deleted_at')],
            'to_city' => ['required', 'string', Rule::exists('cities', 'uuid')->whereNull('deleted_at'), 'different:from_city'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
        ];
    }
}
