<?php

namespace App\requests;

class SignUpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'notBlank|email|stringType',
            'password' => 'notBlank|stringType'
        ];
    }
}