<?php

namespace App\Traits;

use App\Models\aeropay\Transaction; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait HandlesAeroPay
{
    /**
     * Create Transaction with Custom Status & Metadata
     */
    public function createAeroPayPayment($userId, $amount, $referenceId, $partner, $metadata = [], $status = 'pending')
    {
        try {
            $txCode = 'APAY-' . strtoupper(Str::random(10));

            // Direct DB Create
            $transaction = Transaction::create([
                'user_id'              => $userId,
                'transaction_code'     => $txCode,
                'partner'              => $partner,
                'partner_reference_id' => $referenceId, // This will now be the UUID
                'amount'               => $amount,
                'currency'             => 'PHP',
                'status'               => $status,      // <--- Uses the passed status (e.g., 'confirmed')
                'metadata'             => $metadata     // <--- Saves the JSON metadata
            ]);

            return [
                'success' => true,
                'transaction_code' => $transaction->transaction_code,
                'status'           => $transaction->status,
            ];

        } catch (\Exception $e) {
            Log::error("AeroPay Trait Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Payment Error: " . $e->getMessage(),
            ];
        }
    }

    public function updateAeroPayStatus($transactionCode, $status)
    {
        try {
            $transaction = Transaction::where('transaction_code', $transactionCode)->first();
            if ($transaction) {
                $transaction->update(['status' => $status]);
                return ['success' => true, 'data' => $transaction];
            }
            return ['success' => false, 'message' => 'Transaction not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}