<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSender;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;

    protected $title;
    protected $Received;
    protected $cc;
    protected $body;

    public function __construct($title,$Received,$cc,$body)
    {
        $this->title = $title;
        $this->Received = $Received;
        $this->cc = $cc;
        $this->body = $body;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->Received)
            ->cc($this->cc)
            ->send(new EmailSender($this->body,$this->title));
    }
}
