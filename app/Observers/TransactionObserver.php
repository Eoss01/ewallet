<?php

namespace App\Observers;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        DB::transaction(function() use ($wallet, $transaction)
        {
            $wallet->lockForUpdate();

            if ($transaction->transaction_type === TransactionType::Deposit || $transaction->transaction_type === TransactionType::Rebate)
            {
                $wallet->wallet_balance = $wallet->wallet_balance - $transaction->transaction_amount;
                $wallet->update();
            }
            elseif ($transaction->transaction_type === TransactionType::Withdrawal)
            {
                $wallet->wallet_balance = $wallet->wallet_balance + $transaction->transaction_amount;
                $wallet->update();
            }
        });
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
