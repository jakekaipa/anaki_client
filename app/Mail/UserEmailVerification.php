<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $first_name;
    public $code;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->view('mail-templates.user.emailverificatioForm')->subject(__('이메일 인증 해주세요'))->with(['code' => $this->code]);
    }
}
