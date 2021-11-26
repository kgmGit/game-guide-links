<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordFramework;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends ResetPasswordFramework implements ShouldQueue
{
    use Queueable;

    /**
         * Get the reset password notification mail message for the given URL.
         *
         * @param  string  $url
         * @return \Illuminate\Notifications\Messages\MailMessage
         */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('パスワード再設定のお知らせ'))
            ->line(Lang::get('このメールは、パスワードのリセットリクエストを受け取ったため、送信しています。'))
            ->action(Lang::get('パスワードリセット'), $url)
            ->line(Lang::get('このリンクは :count 分で失効します。', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('パスワードの再設定が不要の場合、作業は必要ありません。'));
    }
}
