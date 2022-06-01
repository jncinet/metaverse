<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MainRequest extends FormRequest
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
            'user_id' => ['filled', 'exists:App\Models\User,id'],
            'type' => ['integer'],
            'metaverse_exchange_id' => ['filled', 'exists:Jncinet\Metaverse\Models\MetaverseExchange,id'],
            'amount' => ['numeric'],
            'total' => ['numeric'],
        ];
    }

    public function attributes()
    {
        return trans('metaverse::main');
    }
}
