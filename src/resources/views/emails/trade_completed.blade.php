<p>{{ $trade->seller->name }} さん</p>

<p>購入者があなたを評価しました。</p>

<p>商品名：{{ $trade->order->item->name }}</p>

<p>取引画面を開いて購入者の評価後、取引を完了してください。</p>

<p>
	<a href="{{ route('trades.show', ['trade' => $trade->id]) }}">
		取引画面を開く
	</a>
</p>
