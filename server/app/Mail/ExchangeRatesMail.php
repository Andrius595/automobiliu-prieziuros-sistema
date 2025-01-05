<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** @codeCoverageIgnore */
class ExchangeRatesMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $response)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Valiutos kursas',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $last_update = $this->response['time_last_update_unix'];
        $eurToNok = $this->response['rates']['NOK'];
        $nokToEur = 1/$eurToNok;

        $m = "Rates as of $last_update:\n EUR to NOK: $eurToNok, NOK to EUR: $nokToEur";

        return new Content(
            view: 'mails.exchange_rates',
            with: [
                'message' => $m,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
