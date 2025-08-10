<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'key' => 'required|string',
            'locale' => 'required|string|exists:locales,code',
            'value' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'string',
            'description' => 'nullable|string',
        ];
    }
}
