<?php

namespace App\requests;

use App\http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

abstract class FormRequest
{
    public function __construct(
        protected ServerRequestInterface $request
    ) {}

    public function __call(string $name, array $arguments)
    {
        return $this->request->$name($arguments);
    }

    protected function validateRequest()
    {
        $validations = [];
        foreach($this->rules() as $key => $rule) {
            $rules = explode('|', $rule);
            $validationRules = [];
            foreach($rules as $rule) {
                $validationRules[] = Validator::$rule();
            }
            $validations[] = Validator::key(
                $key,
                Validator::allOf(
                    ...$validationRules
                )
            )->setName($key);
        }

        Validator::allOf(...$validations)->assert($this->request->getParsedBody());
    }

    public abstract function rules(): array;

    public function getRequest()
    {
        return $this->request;
    }
}