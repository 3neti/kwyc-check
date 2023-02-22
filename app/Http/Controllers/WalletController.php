<?php

namespace App\Http\Controllers;

use Inertia\Response;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        $wallet = auth()->user()->getWallet('default');
        return Inertia::render('Wallet/Show', [
            'balance' => $wallet->balanceFloat,
            'updated_at' => $wallet->updated_at
        ]);
    }
}
