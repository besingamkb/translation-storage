<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocaleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        /** @var int|string|null $id */
        $id = method_exists($this, 'route') ? $this->route('id') : null;
        return [
            'code' => 'sometimes|required|string|unique:locales,code,' . $id,
            'name' => 'sometimes|required|string',
        ];
    }
}
