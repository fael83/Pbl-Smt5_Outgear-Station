<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Services\MidtransService;
use App\Support\BadgeBooking;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(MidtransService $midtrans): View
    {
        $peralatan = Alat::query()
            ->with('kategori')
            ->orderBy('nama')
            ->get();

        return view('customer.katalog', [
            'peralatan' => $peralatan,
            'snapJsUrl' => $midtrans->urlSnapJs(),
            'clientKey' => $midtrans->clientKey(),
            'badgeBooking' => BadgeBooking::untuk(auth()->id()),
        ]);
    }
}
