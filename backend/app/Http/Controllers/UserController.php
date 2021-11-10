<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * ユーザ削除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        auth()->guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        DB::beginTransaction();
        try {
            $user->games()->update(['user_id' => null]);
            $user->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, 'ユーザ削除処理中にエラーが発生しました');
        }

        return response()->json(null, 204);
    }
}
