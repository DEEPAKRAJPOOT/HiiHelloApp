<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\BulkSentEmail;
use Mail;

class BulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    protected $email_data, $users;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_data,$users)
    {
        $this->email_data = [
            'subject' => $email_data['subject'] ?? '',
            'message' => $email_data['message'] ?? ''
        ];
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user) {
            Mail::to($user['email'])->queue(new BulkSentEmail($this->email_data,$user));
        }
        $response = [
            'status' => 'success',
            'message' => 'Emails sent successfully'
        ];
        return (object) $response;
    }
}
