<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $type; // 'paid' atau 'delivered'

    public function __construct(Order $order, $type)
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'paid'
            ? "Pembayaran Diterima - Order #{$this->order->order_number}"
            : "Pesanan Telah Sampai - Order #{$this->order->order_number}";

        return $this->subject($subject)
            ->view('emails.order-status');
    }
}