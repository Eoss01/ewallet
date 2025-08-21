<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Jobs\RebateJob;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $from_date = Carbon::today()->subDays(7)->format('Y-m-d');
        $to_date = Carbon::today()->format('Y-m-d');
        $search_value = null;
        $search_type = null;

        $transactions = Transaction::whereBetween('transaction_date', [$from_date, $to_date])->get();

        return view('transactions.index', compact('from_date', 'to_date', 'search_value', 'search_type', 'transactions'));
    }

    public function search(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $search_value = $request->search_value;
        $search_type = $request->search_type;

        $query = Transaction::whereBetween('transaction_date', [$from_date, $to_date]);

        if ($search_value != null)
        {
            $query->where(function($q) use ($search_value)
            {
                $q->whereRelation('wallet.user', 'uid', 'like', '%'.$search_value.'%')
                ->orWhereRelation('wallet.user', 'name', 'like', '%'.$search_value.'%');
            });
        }

        if ($search_type != null)
        {
            $query->where('transaction_type', $search_type);
        }

        $transactions = $query->get();

        return view('transactions.index', compact('from_date', 'to_date', 'search_value', 'search_type', 'transactions'));
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
        $request->validate([
            'user_cid' => 'required',
            'transaction_type' => 'required',
            'transaction_amount' => 'required|numeric|min:1|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ]);

        $user = User::firstWhere('cid', $request->user_cid);

        if (!$user)
        {
            return Redirect::back()->with('error_create_transaction', __('User not found!'))->withInput();
        }

        if (!$user->wallet)
        {
            return Redirect::back()->with('error_create_transaction', __('Wallet not found!'))->withInput();
        }

        $wallet = $user->wallet;

        DB::transaction(function () use ($wallet, $request)
        {
            $wallet->lockForUpdate();

            if ($request->transaction_type === TransactionType::Withdrawal->value && $wallet->wallet_balance < $request->transaction_amount)
            {
                return Redirect::back()->with('error_create_transaction', __('Wallet balance is not enough!'))->withInput();
            }

            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_type' => $request->transaction_type,
                'transaction_date' => Carbon::today(),
                'transaction_amount' => $request->transaction_amount,
            ]);

            if ($request->transaction_type === TransactionType::Withdrawal->value)
            {
                $wallet->wallet_balance -= $request->transaction_amount;
                $wallet->update();
            }
            elseif ($request->transaction_type === TransactionType::Deposit->value)
            {
                $wallet->wallet_balance += $request->transaction_amount;
                $wallet->update();

                $setting = Setting::latest()->first();

                RebateJob::dispatch($transaction, $setting)->afterCommit();
            }
        });

        return Redirect::route('transactions.index')->with('success', __('New ') . __($request->transaction_type) . __(' transaction is created.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $cids = $request->cids;

        $transactions = Transaction::whereIn('cid', $cids)->get();

        foreach($transactions as $transaction)
        {
            $transaction->delete();
        }

        return response()->json([
            'message' => __('Transaction has been deleted.')
        ]);
    }
}
