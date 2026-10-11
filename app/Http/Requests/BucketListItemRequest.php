<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BucketListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('bucketListItem');

        return $item ? $this->user()->can('update', $item) : $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
