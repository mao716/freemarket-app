<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
	public function index(Request $request)
	{
		// ?tab=mylist 指定なら mylist() へ処理を渡す
		if ($request->query('tab') === 'mylist') {
			return $this->mylist($request);
		}
		return view('items.index'); // 一覧
	}

	public function mylist(Request $request)
	{
		// マイリスト一覧（実装は後で）
		return view('items.index');
	}

	public function detail($item_id)
	{
		return view('items.detail', compact('item_id'));
	}
}
