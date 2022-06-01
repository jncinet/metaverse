<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RewardRequest extends FormRequest
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
            'type' => ['integer'],
            'amount' => ['numeric'],
            'user_id' => ['filled', 'exists:App\Models\User,id'],
            'metaverse_power_id' => ['filled', 'exists:Jncinet\Metaverse\Models\MetaversePower,id'],
            'metaverse_reward_id' => ['filled', 'exists:Jncinet\Metaverse\Models\MetaverseReward,id'],
            'metaverse_main_id' => ['filled', 'exists:Jncinet\Metaverse\Models\MetaverseMain,id'],
            'status' => ['integer']
        ];
    }

    public function attributes()
    {
        return trans('metaverse::reward');
    }
}
