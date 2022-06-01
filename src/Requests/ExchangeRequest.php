<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'metaverse_user_level_id' => ['filled', 'exists:articles,id'],
            'user_id' => ['filled', 'exists:App\Models\User,id'],
            'amount' => ['numeric'],
            'rate' => ['numeric'],
            'fees' => ['numeric'],
        ];
    }

    public function attributes()
    {
        return trans('metaverse::exchange');
    }
}
