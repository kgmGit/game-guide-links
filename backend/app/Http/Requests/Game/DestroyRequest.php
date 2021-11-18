<?php

namespace App\Http\Requests\Game;

use App\Models\Game;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DestroyRequest extends FormRequest
{
    public function authorize(): Response
    {
        return Gate::authorize('destroy', [Game::class, $this->route('game')]);
    }

    public function rules()
    {
        return [
            //
        ];
    }
}
