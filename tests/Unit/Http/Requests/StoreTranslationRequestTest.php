<?php

use App\Http\Requests\StoreTranslationRequest;
use Illuminate\Support\Facades\Validator;

describe('StoreTranslationRequest', function () {
    it('authorizes by default', function () {
        $request = new StoreTranslationRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('validates required fields', function () {
        $data = [];
        $validator = Validator::make($data, (new StoreTranslationRequest())->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('key'))->toBeTrue();
        expect($validator->errors()->has('locale'))->toBeTrue();
        expect($validator->errors()->has('value'))->toBeTrue();
    });

    it('validates valid data', function () {
        \App\Models\Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        $data = [
            'key' => 'greeting',
            'locale' => 'en',
            'value' => 'Hello',
            'tags' => ['welcome'],
            'description' => 'A greeting',
        ];
        $validator = Validator::make($data, (new StoreTranslationRequest())->rules());
        expect($validator->passes())->toBeTrue();
    });
});
