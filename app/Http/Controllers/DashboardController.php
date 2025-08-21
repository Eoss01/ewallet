<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('superadministrator') && Auth::user()->status === ActiveStatus::Active)
        {
            $search_value = null;
            $search_type = null;

            $today_deposits = Transaction::where('transaction_type', TransactionType::Deposit)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $today_rebates = Transaction::where('transaction_type', TransactionType::Rebate)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $today_withdrawals = Transaction::where('transaction_type', TransactionType::Withdrawal)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $total_cashflows = Wallet::sum('wallet_balance');

            $transactions = Transaction::where('transaction_date', Carbon::today())->get();

            return view('superadministrators.dashboard', compact('search_value', 'search_type', 'today_deposits', 'today_rebates', 'today_withdrawals', 'total_cashflows', 'transactions'));
        }
        elseif (Auth::user()->hasRole('user') && Auth::user()->status === ActiveStatus::Active)
        {
            $from_date = Carbon::today()->subDays(7)->format('Y-m-d');
            $to_date = Carbon::today()->format('Y-m-d');
            $search_type = null;

            $total_deposits = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Deposit)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_rebates = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Rebate)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_withdrawals = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Withdrawal)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_cashflows = Wallet::where('user_id', Auth::user()->id)->sum('wallet_balance');

            $transactions = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->whereBetween('transaction_date', [$from_date, $to_date])->get();

            return view('users.dashboard', compact('from_date', 'to_date', 'search_type', 'total_deposits', 'total_rebates', 'total_withdrawals', 'total_cashflows', 'transactions'));
        }
        else if (Auth::user()->status === ActiveStatus::Inactive)
        {
            Auth::logout();
            return redirect('login')->with('error', __('Your account is inactive.'));
        }
    }

    public function search(Request $request)
    {
        if (Auth::user()->hasRole('superadministrator') && Auth::user()->status === ActiveStatus::Active)
        {
            $search_value = $request->search_value;
            $search_type = $request->search_type;

            $today_deposits = Transaction::where('transaction_type', TransactionType::Deposit)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $today_rebates = Transaction::where('transaction_type', TransactionType::Rebate)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $today_withdrawals = Transaction::where('transaction_type', TransactionType::Withdrawal)->where('transaction_date', Carbon::today())->sum('transaction_amount');
            $total_cashflows = Wallet::sum('wallet_balance');

            $transactions = Transaction::where('transaction_date', Carbon::today())->get();

            return view('superadministrators.dashboard', compact('search_value', 'search_type', 'today_deposits', 'today_rebates', 'today_withdrawals', 'total_cashflows', 'transactions'));
        }
        elseif (Auth::user()->hasRole('user') && Auth::user()->status === ActiveStatus::Active)
        {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $search_type = $request->search_type;

            $total_deposits = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Deposit)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_rebates = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Rebate)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_withdrawals = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->where('transaction_type', TransactionType::Withdrawal)->whereBetween('transaction_date', [$from_date, $to_date])->sum('transaction_amount');
            $total_cashflows = Wallet::where('user_id', Auth::user()->id)->sum('wallet_balance');

            $transactions = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->whereBetween('transaction_date', [$from_date, $to_date])->when($search_type, function ($query, $search_type) { return $query->where('transaction_type', $search_type); })->get();

            return view('users.dashboard', compact('from_date', 'to_date', 'search_type', 'total_deposits', 'total_rebates', 'total_withdrawals', 'total_cashflows', 'transactions'));
        }
    }
}
