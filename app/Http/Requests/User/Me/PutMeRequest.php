<?php


namespace App\Http\Requests\User\Me;


use App\Http\Requests\FormRequest;

class PutMeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string',
            'birthdate' => 'date'
        ];
    }
}
