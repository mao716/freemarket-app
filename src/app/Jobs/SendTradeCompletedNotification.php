<?php

namespace App\Jobs;

use App\Mail\TradeCompletedNotification;
use App\Models\Trade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTradeCompletedNotification implements ShouldQueue
{
	use Queueable;

	public function __construct(
		private readonly int $tradeId
	) {}

	public function handle(): void
	{
		$trade = Trade::with(['seller', 'order.item'])->findOrFail($this->tradeId);

		Mail::to($trade->seller->email)->send(
			new TradeCompletedNotification($trade)
		);
	}
}
