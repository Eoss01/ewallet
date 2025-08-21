<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $from_date = Carbon::today()->subMonths(1)->format('Y-m-d');
        $to_date = Carbon::today()->format('Y-m-d');
        $search_value = null;
        $search_status = 'active';

        $users = User::role('user')->whereBetween('join_date', [$from_date, $to_date])->where('status', $search_status)->get();

        return view('users.index', compact('from_date', 'to_date', 'search_value', 'search_status', 'users'));
    }

    public function search(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $search_value = $request->search_value;
        $search_status = $request->search_status;

        $query = User::role('user');

        if ($search_value != null)
        {
            $query->where(function($q) use ($search_value)
            {
                $q->where('uid', 'like', '%'.$search_value.'%')
                ->orWhere('name', 'like', '%'.$search_value.'%')
                ->orWhere('email', 'like', '%'.$search_value.'%');
            });
        }
        else
        {
            $query->whereBetween('join_date', [$from_date, $to_date]);

            if ($search_status != null)
            {
                $query->where('status', $search_status);
            }
        }

        $users = $query->get();

        return view('users.index', compact('from_date', 'to_date', 'search_value', 'search_status', 'users'));
    }

    public function find_users(Request $request)
    {
        $query = User::with('wallet')->role('user')->where('status', ActiveStatus::Active);

        if ($search = $request->query('search'))
        {
            $query->where(function ($q) use ($search) {
                $q->where('uid', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
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
            'uid' => 'required|unique:users',
            'name' => 'required|max:255',
            'phone' => 'required|numeric|max:50',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'join_date' => 'required|date',
            'status' => 'required',
        ]);

        if ($request->hasfile('photo'))
        {
            $file = $request->file('photo');
            $filenameExtension = $file->getClientOriginalName();
            $filename = pathinfo($filenameExtension, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filenameToStore = $filename.'_'.date("YmdHis").'.'.$extension;
            $path = $file->storeAs('user_photo', $filenameToStore, 's3');
        }
        else
        {
            $filenameToStore = null;
        }

        $user = User::create([
            'uid' => $request->uid,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $filenameToStore,
            'join_date' => $request->join_date,
            'status' => $request->status,
        ]);

        $user->assignRole('user');

        return Redirect::route('users.index')->with('success', __('New user is created.'));
    }

    /**
     * Display the specified resource.
     */
    public function show($user_cid)
    {
        $user = User::firstWhere('cid', $user_cid);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = User::firstWhere('cid', $request->user_cid);

        if (!$user)
        {
            return Redirect::back()->with('error', __('User not found!'))->withInput();
        }

        $request->validate([
            'edit_uid' => 'required|unique:users,uid,'.$user->id,
            'edit_name' => 'required|max:255',
            'edit_phone' => 'required|numeric|max:50',
            'edit_email' => 'required|email|max:255',
            'edit_password' => 'nullable|min:8|max:255',
            'edit_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'edit_join_date' => 'required|date',
            'edit_status' => 'required',
        ]);

        if ($request->version != $user->version)
        {
            return Redirect::back()->with('error', __('Record has been updated by another user, please try again!'))->withInput();
        }

        if ($request->hasfile('edit_photo'))
        {
            $file = $request->file('edit_photo');
            $filenameExtension = $file->getClientOriginalName();
            $filename = pathinfo($filenameExtension, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filenameToStore = $filename.'_'.date("YmdHis").'.'.$extension;
            $path = $file->storeAs('user_photo', $filenameToStore, 's3');
        }
        else
        {
            $filenameToStore = null;
        }

        $user->update([
            'uid' => $request->edit_uid,
            'name' => $request->edit_name,
            'phone' => $request->edit_phone,
            'email' => $request->edit_email,
            'password' => $request->edit_password != null ? Hash::make($request->edit_password) : $user->password,
            'photo' => $filenameToStore,
            'join_date' => $request->edit_join_date,
            'status' => $request->edit_status,
        ]);

        return Redirect::route('users.index')->with('success', __('User is updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $cids = $request->cids;

        $users = User::whereIn('cid', $cids)->delete();

        foreach($users as $user)
        {
            $user->delete();
        }

        return response()->json([
            'message' => __('User has been deleted.')
        ]);
    }

    public function profile_edit(User $user, $cid)
    {
        if (Auth::user()->cid != $cid)
        {
            return Redirect::route('dashboard')->with('error', __('User does not have any of the necessary access rights.'));
        }

        $user = User::firstWhere('cid', $cid);

        return view('users.profile-edit', compact('user'));
    }

    public function profile_update(Request $request, User $user, $cid)
    {
        if (Auth::user()->cid != $cid)
        {
            return Redirect::route('dashboard')->with('error', __('User does not have any of the necessary access rights.'));
        }

        $validated_data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|confirmed|min:8|max:255',
            'password_confirmation' => 'nullable|min:8|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        $user = User::firstWhere('cid', $cid);

        if ($request->version != $user->version)
        {
            return Redirect::back()->with('error', __('Record has been updated by another user, please try again!'))->withInput();
        }

        $update_user = User::firstWhere('cid', $cid);
        $update_user->name = $request->name;
        $update_user->email = $request->email;
        if ($request->password != null) { $update_user->password = Hash::make($request->password); }

        if ($request->hasFile('photo'))
        {
            $file = $request->file('photo');
            $file_name_to_store = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . date("YmdHis") . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put('user_photo/'.$file_name_to_store, file_get_contents($file));
            $update_user->photo = $file_name_to_store;
        }

        $update_user->update();

        return Redirect::route('profile_edit', ['cid' => $cid])->with('success', __('Your account profile is updated.'));
    }

    public function superadministratorProfileEdit(User $user, $cid)
    {
        if (Auth::user()->cid != $cid)
        {
            return Redirect::route('dashboard')->with('error', __('User does not have any of the necessary access rights.'));
        }

        $superadministrator = User::firstWhere('cid', $cid);

        return view('superadministrators.profile-edit', compact('superadministrator'));
    }

    public function superadministratorProfileUpdate(Request $request, User $user, $cid)
    {
        if (Auth::user()->cid != $cid)
        {
            return Redirect::route('dashboard')->with('error', __('User does not have any of the necessary access rights.'));
        }

        $superadministrator = User::firstWhere('cid', $cid);

        $validated_data = $request->validate([
            'uid' => 'required|max:255|unique:users,uid,'.$superadministrator->id,
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|confirmed|min:8|max:255',
            'password_confirmation' => 'nullable|min:8|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        if ($request->version != $superadministrator->version)
        {
            return Redirect::back()->with('error', __('Record has been updated by another user, please try again!'))->withInput();
        }

        $update_superadministrator = User::firstWhere('cid', $cid);
        $update_superadministrator->uid = $request->uid;
        $update_superadministrator->name = $request->name;
        $update_superadministrator->email = $request->email;
        if ($request->password != null) { $update_superadministrator->password = Hash::make($request->password); }

        if ($request->hasFile('photo'))
        {
            $file = $request->file('photo');
            $file_name_to_store = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . date("YmdHis") . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put('user_photo/'.$file_name_to_store, file_get_contents($file));
            $update_superadministrator->photo = $file_name_to_store;
        }

        $update_superadministrator->update();

        return Redirect::route('superadministrators.profile_edit', ['cid' => $cid])->with('success', __('Your account profile is updated.'));
    }
}
