<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatus;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('superadministrator') && Auth::user()->status === ActiveStatus::Active)
        {
            return view('superadministrators.dashboard');
        }
        elseif (Auth::user()->hasRole('user') && Auth::user()->status === ActiveStatus::Active)
        {
            $from_date = Carbon::today()->subDays(7)->format('Y-m-d');
            $to_date = Carbon::today()->format('Y-m-d');
            $search_value = null;
            $search_type = null;

            $transactions = Transaction::whereRelation('wallet', 'user_id', Auth::user()->id)->whereBetween('transaction_date', [$from_date, $to_date])->get();

            return view('users.dashboard', compact('from_date', 'to_date', 'search_value', 'search_type', 'transactions'));
        }
        else if (Auth::user()->status === ActiveStatus::Inactive)
        {
            Auth::logout();
            return redirect('login')->with('error', __('Your account is inactive.'));
        }
    }

    public function search(Request $request)
    {

    }
}
