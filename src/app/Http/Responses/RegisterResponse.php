<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 新規登録後だけプロフィール設定画面に飛ばす
        return redirect('/mypage/profile');
    }
}
