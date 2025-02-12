<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderContent;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderContent)
    {
        $this->orderContent= $orderContent;
    }

   public function build()
   {

    return $this->markdown('mails.order_placed')->subject('Order Placed')->with(['orderContent'=>$this->orderContent]);

   }
}
