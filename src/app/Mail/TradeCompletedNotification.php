<?php

namespace App\Mail;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TradeCompletedNotification extends Mailable
{
	use Queueable, SerializesModels;

	public Trade $trade;

	public function __construct(Trade $trade)
	{
		$this->trade = $trade;
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: '【COACHTECH】評価をお願いします',
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'emails.trade_completed',
		);
	}
}
