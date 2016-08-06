<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'Значение поля :attribute не является верным URL.',
    'after' => 'Значение поля :attribute должно быть после :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'Значение поля :attribute должно быть только true или false.',
    'confirmed' => 'Значение поля :attribute не совпадает.',
    'date' => 'Значение поля :attribute не дата.',
    'date_format' => 'Значением поля :attribute должна быть дата в формате :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'Поле :attribute должно быть email адресом.',
    'exists' => 'The selected :attribute is invalid.',
    'filled' => 'The :attribute field is required.',
    'image' => 'Значение поля :attribute должно быть изображением.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'Значение поля :attribute должно быть натуральным числом.',
    'ip' => 'The :attribute must be a valid IP address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => 'Значение поля :attribute не должно быть больше чем :max.',
        'file' => 'Значение поля :attribute не должно быть больше чем :max килобайт.',
        'string' => 'Значение поля :attribute не должно быть больше чем :max символов.',
        'array' => 'Значение поля :attribute не должно иметь больше чем :max значений.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'Значение поля :attribute должно быть менее :min.',
        'file' => 'Значение поля :attribute должно быть менее :min килобайт.',
        'string' => 'Значение поля :attribute должно быть менее :min символов.',
        'array' => 'Значение поля :attribute должно иметь менее :min значений.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => 'Значение поля :attribute должно быть числом.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'Значение поля :attribute не соответствует формату.',
    'required' => 'Поле :attribute является обязательным.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'Значение поля :attribute должно быть строкой.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'Такое значение поля :attribute уже занято.',
    'url' => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'custom_numeric' => 'У значения поля :attribute неверный формат числа.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
