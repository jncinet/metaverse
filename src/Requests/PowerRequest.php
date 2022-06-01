<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PowerRequest extends FormRequest
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
            'metaverse_machine_id' => ['filled', 'exists:Jncinet\Metaverse\Models\MetaverseMachine,id'],
            'quantity' => ['integer'],
            'count' => ['integer'],
            'price' => ['numeric'],
            'power' => ['numeric'],
            'remaining_count' => ['integer'],
            'total_price' => ['numeric'],
            'total_power' => ['numeric'],
        ];
    }

    public function attributes()
    {
        return trans('metaverse::power');
    }
}
