<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamLevelRequest extends FormRequest
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
            'name' => ['filled'],
            'big_power' => ['numeric'],
            'small_power' => ['numeric'],
            'main_reward_rate' => ['numeric'],
            'sort' => ['integer'],
        ];
    }

    public function attributes()
    {
        return trans('metaverse::team-level');
    }
}
