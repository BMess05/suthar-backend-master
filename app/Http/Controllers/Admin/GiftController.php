<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\GiftStore;
use App\Models\Store;
use App\Models\User;
use App\Models\ManagersStore;
use App\Http\Requests\GiftRequest;

class GiftController extends Controller
{
    public function list(Request $request) {
        $per_page = config('constants.admin.per_page');

        if(\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores;
            $stores = [];
            foreach($manager_stores as $store) {
                $stores[] = $store->store;
            }
        }
        // dd($stores);
        $store_manager_id = "";
        $store_id = "";
        $data = $request->all();
        if((isset($data['store_manager']) && (trim($data['store_manager']) != "")) || (isset($data['stores']) && (trim($data['stores']) != ""))) {
            $gifts = Gift::query();
            if(\Auth::user()->role == 0) {
                if(isset($data['store_manager']) && (trim($data['store_manager']) != "")) {
                    $gifts->where('created_by', $data['store_manager']);
                    $store_manager_id = $data['store_manager'];
                    $manager_stores = ManagersStore::where('manager_id', $data['store_manager'])->get()->pluck('store_id')->toArray();

                    $gifts_store = GiftStore::select(['gift_id'])->whereIn('store_id', $manager_stores)->get()->pluck('gift_id')->toArray();
                    $gifts->orWhereIn('id', $gifts_store);

                    $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
                }
            }

            if(isset($data['stores']) && (trim($data['stores']) != "")) {
                $gifts_store = GiftStore::select(['gift_id'])->orWhere('store_id', $data['stores'])->get()->pluck('gift_id')->toArray();

                $gifts->orWhereIn('id', $gifts_store);
                $store_id = $data['stores'];
            }
            $gifts = $gifts->orderBy('id', 'DESC')->paginate($per_page);
            // dd($gifts);
        }   else {

            /* Without filters */
            if(\Auth::user()->role == 0) {
                $gifts = Gift::where('is_active', 1)->orderBy('id', 'DESC')->paginate($per_page);
                $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
                $stores = Store::select(['id', 'name', 'city'])->get();
            } else {
                $manager_stores = \Auth::user()->manager_stores()->pluck('store_id')->toArray();
                $giftStores = GiftStore::select(['id', 'gift_id'])->whereIn('store_id', $manager_stores)->orderBy('id', 'DESC')->distinct('gift_id')->paginate($per_page);

                foreach($giftStores as $i => $gift_store) {
                    $giftStores[$i] = $gift_store->gift;
                }
                $gifts = $giftStores;
            }
        }

        $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
        return view('admin.gifts.list', compact('gifts', 'store_managers', 'stores', 'store_manager_id', 'store_id'));
    }

    public function add() {
        if(\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores()->pluck('store_id')->toArray();
            $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
        }
        return view('admin.gifts.add', compact('stores'));
    }

    public function save(GiftRequest $request) {
        $data = $request->all();
        $gift = new Gift();
        // dd($data);
        if($gift->saveGift($data)) {
            return redirect()->route('listGifts')->with(['status' => 'success', 'message' => 'Gift added successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function edit($id) {
        $gift = Gift::find($id);
        if(!$gift) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        $giftStores = $gift->gift_stores()->pluck('store_id')->toArray();
        if(\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores()->pluck('store_id')->toArray();
            $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
        }
        return view('admin.gifts.edit', compact('gift', 'stores', 'giftStores'));
    }

    public function update($id, GiftRequest $request) {
        $gift = Gift::find($id);
        if(!$gift) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        $data = $request->all();
        if($gift->saveGift($data)) {
            return redirect()->route('listGifts')->with(['status' => 'success', 'message' => 'Gift updated successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function delete($id) {
        $gift = Gift::find($id);
        if(!$gift) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        if($gift->delete()) {
            GiftStore::where('gift_id', $id)->delete();
            return redirect()->route('listGifts')->with(['status' => 'success', 'message' => 'Gift deleted successfully.']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

}
