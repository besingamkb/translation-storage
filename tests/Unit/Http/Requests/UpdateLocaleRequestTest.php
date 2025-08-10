<?php

use App\Http\Requests\UpdateLocaleRequest;
use Illuminate\Support\Facades\Validator;

describe('UpdateLocaleRequest', function () {
    it('authorizes by default', function () {
        $request = new UpdateLocaleRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('validates required fields when present', function () {
        $data = [];
        $validator = Validator::make($data, (new UpdateLocaleRequest())->rules());
        expect($validator->passes())->toBeTrue(); // No required fields if not present
    });

    it('validates valid data', function () {
        $data = ['code' => 'en', 'name' => 'English'];
        $validator = Validator::make($data, (new UpdateLocaleRequest())->rules());
        expect($validator->passes())->toBeTrue();
    });
});
