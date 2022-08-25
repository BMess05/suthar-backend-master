<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\ManagersStore;
use App\Models\Gift;
use App\Models\Contractor;
use App\Models\Reference;
use App\Models\RedeemRequest;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function dashboard() {
        if(\Auth::user()->role == 0) {
            $contractors = Contractor::count();
            $managers = User::where('role', 1)->count();
            $stores = Store::count();
            $gifts = Gift::count();

            $references = Reference::count();
            $redeemRequests = RedeemRequest::count();
            return view('admin.dashboard', compact(['contractors', 'managers', 'references', 'redeemRequests', 'stores', 'gifts']));
        }   else {
            $contractors = Contractor::where('created_by', \Auth::user()->id)->count();
            // $manager_stores = ManagersStore::where('manager_id', \Auth::user()->id)->get()->pluck('store_id')->toArray();
            $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
            $references = Reference::whereIn('created_by', $managerContractors)->count();
            $redeemRequests = RedeemRequest::whereIn('contractor_id', $managerContractors)->count();
            return view('admin.dashboard', compact(['contractors', 'references', 'redeemRequests']));
        }

    }
    public function listUsers() {
        $per_page = config('constants.admin.per_page');
        $users = User::where('role', 1)->orderBy('id', 'DESC')->paginate($per_page);
        return view('admin.users.list_users', compact('users'));
    }

    public function add() {
        $assigned_stores = ManagersStore::select('store_id')->distinct()->get();
        $stores_ids = [];
        foreach($assigned_stores as $store) {
            $stores_ids[] = $store->store_id;
        }
        $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->whereNotIn('id', $stores_ids)->get();
        return view('admin.users.add_user', compact('stores'));
    }

    public function save(UserRequest $request) {
        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid.'])->withInput();
        }
        $phone = (int) $data['phone_number'];
        if(strlen($phone) < 10) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add a valid phone number.'])->withInput();
        }
        $user = new User();
        if($user->saveStoreManager($data)) {
            return redirect()->route('listUsers')->with(['status' => 'success', 'message' => 'Manager added successfully.']);
        }   else {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function edit($id) {
        $manager = User::find($id);
        if(!$manager) {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        $manager_stores = [];
        foreach($manager->manager_stores as $mstore) {
            $manager_stores[] = $mstore->store_id;
        }
        $assigned_stores = ManagersStore::select('store_id')->whereNoTIn('store_id', $manager_stores)->distinct()->get();
        $stores_ids = [];
        foreach($assigned_stores as $store) {
            $stores_ids[] = $store->store_id;
        }
        $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->whereNotIn('id', $stores_ids)->get();
        return view('admin.users.edit_user', compact('stores', 'manager', 'manager_stores'));
    }

    public function update($id, UserRequest $request) {
        $manager = User::find($id);
        if(!$manager) {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        $data = $request->validated();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid.'])->withInput();
        }

        $phone = (int) $data['phone_number'];
        if(strlen($phone) < 10) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add a valid phone number.'])->withInput();
        }
        if($manager->saveStoreManager($data)) {
            return redirect()->route('listUsers')->with(['status' => 'success', 'message' => 'Manager updated successfully.']);
        }   else {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function delete($id) {
        $manager = User::find($id);
        if(!$manager) {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        if($manager->delete()) {
            ManagersStore::where('manager_id', $id)->delete();
            return redirect()->route('listUsers')->with(['status' => 'success', 'message' => 'Manager deleted successfully.']);
        }   else {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function view($id) {
        $manager = User::find($id);
        if(!$manager) {
            return redirect()->back()->withInput()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        return view('admin.users.view_user', compact('manager'));
    }

    public function activeInactiveUser(Request $request) {
        $data = $request->all();
        $user = User::find($data['id']);
        if($user && (\Auth::user()->role_id == 0)) {
            if($user->is_active == 0) {
                $user->is_active = 1;
                $msg = "Store manager set to active.";
            }   else {
                $user->is_active = 0;
                $msg = "Store manager set to inactive.";
            }
            if($user->save()) {
                return response()->json([
                    "success" => 1,
                    "message" => $msg
                ]);
            }   else {
                return response()->json([
                    "success" => 0,
                    "message" => 'Something went wrong, try again.'
                ]);
            }
        }   else {
            return response()->json([
                "success" => 0,
                "message" => 'Invalid input.'
            ]);
        }
    }

    public function getManagerStores(Request $request) {
        $data = $request->all();
        $user = User::find($data['selected_manager']);
        if($user) {
            $stores = ManagersStore::where('manager_id', $data['selected_manager'])->get();
            $options = '<option value="" selected disabled>All</option>';
            foreach($stores as $store) {
                $options .= '<option value="'.$store->store_id.'">'.$store->store->name.' ('.$store->store->city.')</option>';
            }
            return response()->json([
                "success" => 1,
                "options" => $options
            ]);

        }   else {
            return response()->json([
                "success" => 0,
                "message" => 'Invalid manager.'
            ]);
        }
    }
}
