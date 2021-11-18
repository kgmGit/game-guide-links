<?php

namespace App\Http\Requests\Site;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    public function authorize(): Response
    {
        return Gate::authorize('update', [Site::class, $this->route('site')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:20',
            'url' => 'required|max:255|regex:/https?:\/\/[^\s]+$/',
            'description' => 'required|max:200',
        ];
    }
}
