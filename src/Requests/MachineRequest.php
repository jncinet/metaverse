<?php

namespace Jncinet\Metaverse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineRequest extends FormRequest
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
            'name' => ['required'],
            'count' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'power' => ['required', 'numeric'],
            'status' => ['integer'],
            'sort' => ['integer'],
        ];
    }

    public function attributes()
    {
        return trans('metaverse::machine');
    }
}
