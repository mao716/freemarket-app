<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller
{
	public function store(Request $request, Item $item)
	{
		$data = $request->validate([
			'body' => ['required', 'string', 'max:255'],
		]);

		$item->comments()->create([
			'user_id' => Auth::id(),
			'body'    => $data['body'],
		]);

		return redirect()->to(route('items.detail', $item) . '#comments')
			->with('status', 'コメントを投稿しました。');
	}
}
