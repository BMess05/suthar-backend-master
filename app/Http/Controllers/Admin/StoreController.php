<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Http\Requests\StoreRequest;
use App\Models\ManagersStore;
use App\Models\District;

class StoreController extends Controller
{
    public function list() {
        $per_page = config('constants.admin.per_page');
        $stores = Store::orderBy('id', 'DESC')->paginate($per_page);
        return view('admin.stores.list', compact('stores'));
    }

    public function add() {
        $cities = District::get()->pluck('district')->toArray();
        // $cities = config('constants.cities');
        // dd($cities);
        return view('admin.stores.add', compact('cities'));
    }

    public function save(StoreRequest $request) {
        $data = $request->validated();
        $store = new Store();
        if($store->saveStore($data)) {
            return redirect()->route('listStores')->with(['status' => 'success', 'message' => 'Store added successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function edit($id) {
        $store = Store::find($id);
        if(!$store) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        // $cities = config('constants.cities');
        $cities = District::get()->pluck('district')->toArray();
        return view('admin.stores.edit', compact('cities', 'store'));
    }

    public function update($id, StoreRequest $request) {
        $data = $request->validated();
        $store = Store::find($id);
        if(!$store) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        if($store->saveStore($data)) {
            return redirect()->route('listStores')->with(['status' => 'success', 'message' => 'Store updated successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function delete($id) {
        $store = Store::find($id);
        if(!$store) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid ID.']);
        }
        if($store->delete()) {
            ManagersStore::where('store_id', $id)->delete();
            return redirect()->route('listStores')->with(['status' => 'success', 'message' => 'Store deleted successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

}
