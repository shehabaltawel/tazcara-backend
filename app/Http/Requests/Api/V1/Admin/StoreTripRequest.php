<?php

namespace App\Http\Requests\Api\V1\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Throwable;

/**
 * Store Trip Request
 */
class StoreTripRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'bus_id' => ['required', 'string', Rule::exists('buses', 'uuid')->whereNull('deleted_at')],
            'stops' => ['required', 'array', 'min:2', 'max:30'],
            'stops.*.city_id' => ['required', 'string', 'distinct', Rule::exists('cities', 'uuid')->whereNull('deleted_at')],
            'stops.*.price_from_origin' => ['required', 'numeric', 'min:0'],
            'stops.*.departure_timestamp' => ['required', 'date', 'after_or_equal:today'],
            'stops.*.arrival_timestamp' => ['required', 'date'],
        ];
    }

    /**
     * Validate the ordering of the trip's stops: prices must rise from the
     * origin, and timestamps must move forward through the sequence.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $stops = $this->input('stops');

            if (! is_array($stops)) {
                return;
            }

            $parse = static function (mixed $value): ?Carbon {
                if (! $value) {
                    return null;
                }

                try {
                    return Carbon::parse($value);
                } catch (Throwable) {
                    return null;
                }
            };

            $previous = null;

            foreach ($stops as $index => $stop) {
                $departure = $parse($stop['departure_timestamp'] ?? null);
                $arrival = $parse($stop['arrival_timestamp'] ?? null);
                $price = isset($stop['price_from_origin']) ? (float) $stop['price_from_origin'] : null;

                if ($departure && $arrival && $arrival->lt($departure)) {
                    $validator->errors()->add("stops.$index.arrival_timestamp", 'The arrival must not be before the departure.');
                }

                if ($previous && $departure && $previous['arrival'] && $departure->lt($previous['arrival'])) {
                    $validator->errors()->add("stops.$index.departure_timestamp", 'Stops must be ordered chronologically.');
                }

                if ($previous && $price !== null && $previous['price'] !== null && $price <= $previous['price']) {
                    $validator->errors()->add("stops.$index.price_from_origin", 'Prices must strictly increase from the origin.');
                }

                $previous = [
                    'arrival' => $arrival,
                    'price' => $price,
                ];
            }

            if (isset($stops[0]['price_from_origin']) && (float) $stops[0]['price_from_origin'] !== 0.0) {
                $validator->errors()->add('stops.0.price_from_origin', 'The origin stop price must be zero.');
            }
        });
    }
}
