<?php

namespace App\requests;

class ProductCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'notBlank|stringType',
            'price' => 'notBlank|numericVal|Positive'
        ];
    }
}