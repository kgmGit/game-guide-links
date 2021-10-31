<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailFramework;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class VerifyEmail extends VerifyEmailFramework
{
    use Queueable;

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('メールアドレスの検証'))
            ->line(Lang::get('メールアドレスの検証を行うため下記のボタンをクリックしてください。'))
            ->action(Lang::get('メールアドレスの検証'), $url)
            ->line(Lang::get('もしアカウントを作成していない場合は追加の処理は必要ありません。'));
    }
}
