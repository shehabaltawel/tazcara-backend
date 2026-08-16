<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Get Available Trip Seats Request
 */
class GetAvailableTripSeatsRequest extends FormRequest
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
            'from_city' => ['required', 'string', Rule::exists('cities', 'uuid')->whereNull('deleted_at')],
            'to_city' => ['required', 'string', Rule::exists('cities', 'uuid')->whereNull('deleted_at'), 'different:from_city'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
