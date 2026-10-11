<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoneyFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m', Rule::prohibitedIf($this->filled('start_date') || $this->filled('end_date'))],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', ...($this->filled('start_date') ? ['after_or_equal:start_date'] : [])],
        ];
    }
}
