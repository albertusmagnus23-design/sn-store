<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtransService): JsonResponse
    {
        $notification = $request->validate([
            'order_id' => ['required', 'string', 'max:40'],
            'status_code' => ['required', 'string', 'regex:/^\d{3}$/'],
            'gross_amount' => ['required', 'string', 'regex:/^\d+(?:\.\d{1,2})?$/'],
            'signature_key' => ['required', 'string', 'regex:/^[a-fA-F0-9]{128}$/'],
            'merchant_id' => ['required', 'string', 'max:40'],
            'transaction_status' => ['required', 'string', 'max:30'],
            'fraud_status' => ['sometimes', 'nullable', 'string', 'max:30'],
            'transaction_id' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $midtransService->handleNotification($notification);

        return response()->json(['status' => 'ok']);
    }
}
