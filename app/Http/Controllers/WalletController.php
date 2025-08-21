<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Jobs\RebateJob;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function balance(Wallet $wallet)
    {
        if (!$wallet)
        {
            abort(400, 'Wallet not found.');
        }

        return response()->json([
            'wallet_cid' => $wallet->cid,
            'balance' => $wallet->wallet_balance,
        ]);
    }

    public function transactions(Request $request, Wallet $wallet)
    {
        if (!$wallet)
        {
            abort(400, 'Wallet not found.');
        }

        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $query = $wallet->transactions();

        if ($request->has(['from_date', 'to_date']))
        {
            $query->whereBetween('transaction_date', [$request->from_date, $request->to_date]);
        }

        return response()->json([
            'transactions' => $query->orderByDesc('created_at')->get(),
        ]);
    }

    public function deposit(Request $request, Wallet $wallet)
    {
        if (!$wallet)
        {
            abort(400, 'Wallet not found.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ]);

        DB::transaction(function () use ($wallet, $request)
        {
            $wallet->lockForUpdate();

            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_type' => TransactionType::Deposit,
                'transaction_date' => Carbon::today(),
                'transaction_amount' => $request->amount,
            ]);

            $wallet->wallet_balance += $request->amount;
            $wallet->update();

            $setting = Setting::latest()->first();

            RebateJob::dispatch($transaction, $setting)->afterCommit();
        });

        return response()->json([
            'message' => 'Deposit successful. Rebate will be processed asynchronously.',
            'balance' => $wallet->fresh()->wallet_balance
        ], 201);
    }

    public function withdraw(Request $request, Wallet $wallet)
    {
        if (!$wallet)
        {
            abort(400, 'Wallet not found!');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ]);

        DB::transaction(function () use ($wallet, $request)
        {
            $wallet->lockForUpdate();

            if ($wallet->wallet_balance < $request->amount)
            {
                abort(400, 'Wallet balance is not enough!');
            }

            Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_type' => TransactionType::Withdrawal,
                'transaction_date' => Carbon::today(),
                'transaction_amount' => $request->amount,
            ]);

            $wallet->wallet_balance -= $request->amount;
            $wallet->update();
        });

        return response()->json([
            'message' => 'Withdrawal successful.',
            'balance' => $wallet->fresh()->wallet_balance
        ], 201);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
