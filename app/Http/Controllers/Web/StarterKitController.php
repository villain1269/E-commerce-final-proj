<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\StarterKitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StarterKitController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly StarterKitService $kits,
    ) {
    }

    /**
     * Show the dedicated Starter Kit Builder page.
     *
     * Renders the form. If a kit result was flashed to the session
     * (from a previous POST), it renders the result card too.
     */
    public function show(Request $request): View
    {
        return view('pages.starter-kit', [
            'kitBusinessLabels' => $this->kits->businessLabels(),
            'kit'               => session('kit'),  // null if no result yet
        ]);
    }

    /**
     * Recommend a kit based on the chosen business type and budget,
     * then optionally add it to the cart.
     */
    public function recommend(Request $request)
    {
        $data = $request->validate([
            'businessType' => ['required', 'in:home,stall,shop'],
            'budget'        => ['required', 'in:3000,5000,10000'],
            'add_to_cart'   => ['sometimes', 'accepted'],
        ]);

        $type = $data['businessType'];
        $budget = (int) $data['budget'];

        $kit = $this->kits->get($type, $budget);

        if (!$kit) {
            return redirect()->route('starter-kit.show')
                ->with('status', 'We could not find a kit for that combination.');
        }

        $items = collect($kit['items'])->map(fn ($pair) => [
            'id'      => $pair[0],
            'qty'     => $pair[1],
            'product' => \App\Models\Product::where('public_id', $pair[0])->first(),
        ])->filter(fn ($i) => $i['product'] !== null);

        $total = $items->reduce(fn ($s, $i) => $s + $i['product']->price * $i['qty'], 0);
        $left  = $budget - $total;

        // If user clicked the "Add entire kit to cart" button on the result panel
        if (!empty($data['add_to_cart'])) {
            $this->cart->addKit($kit['items']);
            return redirect()->route('cart.index')->with('status', 'Kit added to cart.');
        }

        // Flash the kit data to the session and redirect back to the GET /starter-kit page
        return redirect()->route('starter-kit.show')->withInput()->with('kit', [
            'type'       => $type,
            'budget'     => $budget,
            'kit'        => $kit,
            'items'      => $items,
            'total'      => $total,
            'left'       => $left,
            'business_label' => StarterKitService::BUSINESS_LABELS[$type],
        ]);
    }
}
