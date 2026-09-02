<?php

namespace app\controller;

use support\Request;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use WebmanTech\LaravelValidation\Facades\Validator;

class ValidatorController
{
    public function index(Request $request): \support\Response
    {
        Validator::reset();
        $results = [];

        // instance
        $results['instance'] = Validator::instance() instanceof FactoryContract;
        $results['helper'] = validator() instanceof FactoryContract;
        $results['make'] = validator(['title' => '123']) instanceof ValidatorContract;

        // validate ok
        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'required|string'];
        $validated = Validator::validate($data, $rules);
        $results['validate_ok'] = $validated === $data;

        // validate error
        try {
            Validator::validate(['email' => '123'], ['email' => 'required|email']);
            $results['validate_error'] = false;
        } catch (\Throwable $e) {
            $results['validate_error'] = $e instanceof ValidationException;
        }

        // manual validate
        $validator = Validator::make($data, $rules);
        $results['manual_ok'] = $validator->fails() === false;
        $results['manual_validated'] = $validator->validated() === $data;

        $validator = Validator::make(['email' => '123'], ['email' => 'required|email']);
        $results['manual_fails'] = $validator->fails() === true;
        $results['manual_errors'] = $validator->errors()->count() === 1;

        // custom messages
        try {
            Validator::validate(['title' => ''], ['title' => 'required'], ['title.required' => '标题不能为空']);
            $results['custom_message'] = false;
        } catch (ValidationException $e) {
            $results['custom_message'] = $e->errors()['title'][0] === '标题不能为空';
        }

        // translation messages (zh_CN)
        try {
            Validator::validate(['title' => ''], ['title' => 'required']);
            $results['trans_message'] = false;
        } catch (ValidationException $e) {
            $results['trans_message'] = $e->errors()['title'][0] === 'title 不能为空。';
        }

        // switch locale
        $locale = locale();
        locale('en');
        try {
            Validator::validate(['title' => ''], ['title' => 'required']);
            $results['trans_message_en'] = false;
        } catch (ValidationException $e) {
            $results['trans_message_en'] = $e->errors()['title'][0] === 'The title field is required.';
        }
        locale($locale);

        return json($results);
    }
}
