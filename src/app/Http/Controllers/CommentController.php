<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;

class CommentController extends Controller
{
	public function store(CommentRequest $request, Item $item)
	{
		$validated = $request->validated();

		$item->comments()->create([
			'user_id' => $request->user()->id,
			'body'    => $validated['body'],
		]);

		return redirect()->to(route('items.detail', $item) . '#comments');
	}
}
