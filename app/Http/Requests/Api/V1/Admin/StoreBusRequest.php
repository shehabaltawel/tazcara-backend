<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Bus Request
 */
class StoreBusRequest extends FormRequest
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
            'class' => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('buses', 'plate_number')->whereNull('deleted_at')],
            'seats' => ['nullable', 'array', 'max:60'],
            'seats.*' => ['required', 'string', 'distinct'],
        ];
    }
}
