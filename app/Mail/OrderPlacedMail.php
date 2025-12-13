<?php

namespace App\Mail;

use App\Models\CompanyInfo;
use App\Models\EmailTemplate;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?EmailTemplate $template;
    public ?CompanyInfo $companyInfo;
    public string $buttonUrl;

    public function __construct(Order $order)
    {
        // Make sure all needed relations are loaded
        $this->order = $order->load([
            'items.dish',
            'items.crust',
            'items.bun',
        ]);

        $this->template = EmailTemplate::where('key', 'single_order')->first()
            ?? new EmailTemplate();

        $this->companyInfo = CompanyInfo::first()
            ?? new CompanyInfo();

        $this->buttonUrl = route('orders.thankyou', ['code' => $order->order_code]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed — #' . $this->order->order_code,
        );
    }

    public function content(): Content
    {
        // When a mailable is queued, Eloquent models are re-retrieved
        // without any eager loaded relations. Ensure relations are
        // loaded here so the view always gets items, dish, crust, bun.
        $order = $this->order;
        try {
            $order = $order->loadMissing(['items.dish', 'items.crust', 'items.bun']);
        } catch (\Throwable $e) {
            // Fallback: try to reload from DB by id if possible
            if (isset($order->id)) {
                $order = Order::with(['items.dish', 'items.crust', 'items.bun'])->find($order->id) ?? $order;
            }
        }

        return new Content(
            view: 'emails.orders.placed',
            with: [
                'order'       => $order,
                'template'    => $this->template,
                'companyInfo' => $this->companyInfo,
                'buttonUrl'   => $this->buttonUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
