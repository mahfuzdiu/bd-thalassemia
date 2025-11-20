<?php

namespace App\Jobs;

use App\Mail\OrderUpdateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdateEmail implements ShouldQueue
{
    use Queueable;

    public $email;
    public $name;
    public $orderNum;
    public $orderStatus;


    /**
     * Create a new job instance.
     */
    public function __construct($email, $name, $orderNum, $orderStatus)
    {
        $this->email = $email;
        $this->name = $name;
        $this->orderNum = $orderNum;
        $this->orderStatus = $orderStatus;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new OrderUpdateMail($this->name, $this->orderNum, $this->orderStatus));
    }
}
