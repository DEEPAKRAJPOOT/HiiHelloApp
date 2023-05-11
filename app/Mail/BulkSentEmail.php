<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class BulkSentEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $email_data;
    protected $user_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_data,$user_data)
    {
        $this->email_data = $email_data;
        $this->user_data = $user_data;
        $this->subject($email_data['subject'] ?? 'Hi Hello Sent you a mail');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bulk_email')
            ->with([
                'user_name'  => $this->user_data['user_trans_default']['full_name'] ?? $user['full_name'] ?? 'User',
                'email_content'  => $this->buildDynamicContent($this->email_data['message'] ?? '',$this->user_data),
            ]);
    }

    private function buildDynamicContent($message,$user){
        if(Str::contains($message,'#(user_name)')){
            $user_name = $user['user_trans_default']['full_name'] ?? $user['full_name'] ?? 'User';
            $message = Str::replace('#(user_name)',$user_name,$message);
        }
        if(Str::contains($message,'#(user_email)')){
            $user_email = $user['email'] ?? 'your email';
            $message = Str::replace('#(user_email)',$user_email,$message);
        }
        return $message;
    }
}
