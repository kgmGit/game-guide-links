<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use App\Rules\MaxByte;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::authorize('update', [Article::class, $this->route('article')]);
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
            'outline' => 'required|max:200',
            'content' => ['required' , new MaxByte(6000)],
        ];
    }
}
