<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'seats' => ['required', 'array', 'min:1', 'max:12'],
            'seats.*' => ['required', 'string', 'distinct', 'exists:seats,uuid'],
            'from_city' => ['required', 'string', 'exists:cities,uuid'],
            'to_city' => ['required', 'string', 'exists:cities,uuid', 'different:from_city'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
