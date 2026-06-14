<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Transaction $transaction;
    public string $cashierName;

    public function __construct(Transaction $transaction, string $cashierName)
    {
        $this->transaction = $transaction;
        $this->cashierName = $cashierName;
    }

    /**
     * Channel broadcast: public 'pos' channel agar semua client (admin & kasir) menerima.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('pos'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transaction.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'total_amount'   => $this->transaction->total_amount,
            'payment_method' => $this->transaction->payment_method,
            'cashier'        => $this->cashierName,
            'created_at'     => $this->transaction->created_at->toISOString(),
        ];
    }
}