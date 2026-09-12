<?php

namespace App\Http\Controllers;

use App\Models\PriceReference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceReferenceController extends Controller
{
    public function index(Request $request): View
    {
        $prices = $request->user()->priceReferences()
            ->when($request->string('q')->trim()->value(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return view('prices.index', ['prices' => $prices]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->priceReferences()->create($this->validated($request));

        return redirect()
            ->route('prices.index')
            ->with('status', 'Harga berhasil ditambahkan.');
    }

    public function update(Request $request, PriceReference $price): RedirectResponse
    {
        $this->authorizeOwner($request, $price);

        $price->update($this->validated($request));

        return redirect()
            ->route('prices.index')
            ->with('status', 'Harga berhasil diperbarui.');
    }

    public function destroy(Request $request, PriceReference $price): RedirectResponse
    {
        $this->authorizeOwner($request, $price);

        $price->delete();

        return redirect()
            ->route('prices.index')
            ->with('status', 'Harga berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function authorizeOwner(Request $request, PriceReference $price): void
    {
        abort_unless($price->user_id === $request->user()->id, 403);
    }
}
