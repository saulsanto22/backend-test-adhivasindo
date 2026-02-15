<?php

namespace App\Http\Requests\External;

use App\Http\Requests\ApiFormRequest;

class SearchExternalRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string'],
            'nim' => ['sometimes', 'required', 'string'],
            'ymd' => ['sometimes', 'required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $provided = collect(['name', 'nim', 'ymd'])
                ->filter(fn ($key) => $this->filled($key));

            if ($provided->count() !== 1) {
                $validator->errors()->add('filter', 'Harus mengisi tepat satu parameter: name, nim, atau ymd.');
            }
        });
    }

    public function getSearchField(): string
    {
        $map = ['name' => 'NAMA', 'nim' => 'NIM', 'ymd' => 'YMD'];

        foreach ($map as $param => $field) {
            if ($this->filled($param)) {
                return $field;
            }
        }

        return '';
    }

    public function getSearchValue(): string
    {
        foreach (['name', 'nim', 'ymd'] as $param) {
            if ($this->filled($param)) {
                return $this->input($param);
            }
        }

        return '';
    }
}
