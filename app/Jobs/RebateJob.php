<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RebateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Transaction $transaction;
    protected Setting $setting;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction, Setting $setting)
    {
        $this->transaction = $transaction;
        $this->setting = $setting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wallet = $this->transaction->wallet;
        $rebateAmount = $this->transaction->transaction_amount * ($this->setting->rebate_percent / 100);

        DB::transaction(function () use ($wallet, $rebateAmount)
        {

            $wallet->lockForUpdate();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_type' => TransactionType::Rebate,
                'transaction_amount' => $rebateAmount,
                'transaction_date' => Carbon::today(),
            ]);

            $wallet->wallet_balance += $rebateAmount;
            $wallet->update();
        });
    }
}
