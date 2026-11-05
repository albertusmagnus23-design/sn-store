<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutShippingRequest;
use App\Services\RajaOngkirException;
use App\Services\RajaOngkirShippingService;
use Illuminate\Http\JsonResponse;

class CheckoutShippingController extends Controller
{
    public function __invoke(CheckoutShippingRequest $request, RajaOngkirShippingService $shippingService): JsonResponse
    {
        $cartItems = $request->user()->cartItems()
            ->with('product')
            ->orderBy('id')
            ->get();

        try {
            return response()->json($shippingService->createQuote(
                $request->user(),
                $cartItems,
                $request->validated(),
            ));
        } catch (RajaOngkirException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }
}
