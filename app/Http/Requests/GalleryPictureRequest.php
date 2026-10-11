<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryPictureRequest extends FormRequest
{
    public function authorize(): bool
    {
        $picture = $this->route('galleryPicture');

        return $picture ? $this->user()->can('update', $picture) : $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'caption' => ['required', 'string', 'max:2000'],
            'location' => ['required', 'string', 'max:120'],
            'picture' => [$this->route('galleryPicture') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=8000,max_height=8000'],
        ];
    }
}
