<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoneyEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $entry = $this->route('moneyEntry');

        return $entry ? $this->user()->can('update', $entry) : $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,spending'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:2000'],
            'entry_date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
