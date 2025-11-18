<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExhibitionController extends Controller
{
	public function showSellForm(): View
	{
		$categories = Category::orderBy('name')->get();

		return view('items.create', compact('categories'));
	}

	public function productRegister(ExhibitionRequest $request): RedirectResponse
	{
		$validated = $request->validated();

		DB::transaction(function () use ($validated, $request) {
			$imagePath = $request->file('image')->store('items', 'public');

			$item = Item::create([
				'user_id'     => Auth::id(),
				'name'        => $validated['name'],
				'brand'       => $validated['brand'],
				'description' => $validated['description'],
				'price'       => $validated['price'],
				'condition'   => $validated['condition'],
				'image_path'  => $imagePath,
			]);

			$item->categories()->sync($validated['categories']);
		});

		return redirect()->route('items.index');
	}
}
