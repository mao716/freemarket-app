<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;

class CommentController extends Controller
{
	public function store(CommentRequest $request, Item $item)
	{
		$body = $request->validated()['body'];

		$item->comments()->create([
			'user_id' => $request->user()->id,
			'body'    => $body,
		]);

		return redirect()->to(route('items.detail', $item) . '#comments');
	}
}
