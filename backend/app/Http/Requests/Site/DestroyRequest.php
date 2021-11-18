<?php

namespace App\Http\Requests\Site;

use App\Models\Site;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DestroyRequest extends FormRequest
{
    public function authorize(): Response
    {
        return Gate::authorize('destroy', [Site::class, $this->route('site')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
