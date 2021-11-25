<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendResetPassword($address, $login, $token)
    {
        $obj = new \stdClass();
        $obj->login = $login;
        $obj->link = 'http://localhost:3000/password-reset/'.$token;

        Mail::to($address)->send(new ResetPasswordEmail($obj));
    }
}
