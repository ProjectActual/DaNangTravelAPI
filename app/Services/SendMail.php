<?php

namespace App\Services;

use Mail;
use App\Mail\MailAdapter;

class SendMail
{
    public static function send($to, $subject, $view, $info = [])
    {
        Mail::to($to)
            ->send(new MailAdapter($subject, $view, $info));
    }
}
