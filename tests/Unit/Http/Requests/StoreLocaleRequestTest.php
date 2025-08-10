<?php

use App\Http\Requests\StoreLocaleRequest;
use Illuminate\Support\Facades\Validator;

describe('StoreLocaleRequest', function () {
    it('authorizes by default', function () {
        $request = new StoreLocaleRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('validates required fields', function () {
        $data = [];
        $validator = Validator::make($data, (new StoreLocaleRequest())->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('code'))->toBeTrue();
        expect($validator->errors()->has('name'))->toBeTrue();
    });

    it('validates valid data', function () {
        $data = ['code' => 'en', 'name' => 'English'];
        $validator = Validator::make($data, (new StoreLocaleRequest())->rules());
        expect($validator->passes())->toBeTrue();
    });
});
