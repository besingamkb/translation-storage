<?php

use App\Http\Requests\UpdateTranslationValueRequest;
use Illuminate\Support\Facades\Validator;

describe('UpdateTranslationValueRequest', function () {
    it('authorizes by default', function () {
        $request = new UpdateTranslationValueRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('validates required value field', function () {
        $data = [];
        $validator = Validator::make($data, (new UpdateTranslationValueRequest())->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('value'))->toBeTrue();
    });

    it('validates valid data', function () {
        $data = ['value' => 'Hello'];
        $validator = Validator::make($data, (new UpdateTranslationValueRequest())->rules());
        expect($validator->passes())->toBeTrue();
    });
});
