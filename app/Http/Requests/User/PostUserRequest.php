<?php


namespace App\Http\Requests\User;


use App\Http\Requests\Request;

class PostUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    public function attributes()
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'old_password' => 'required_with:password',
            'password' => 'required_with:old_password|confirmed',
        ];
    }
}
