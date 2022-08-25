<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contractor;
use App\Models\Store;
use App\Models\User;
use App\Models\ManagersStore;
use App\Http\Requests\ContractorRequest;
use Helper;
use App\Events\ContractorRegistered;

class ContractorController extends Controller
{
    public function list(Request $request)
    {
        $per_page = config('constants.admin.per_page');
        $store_manager_id = "";
        $store_id = "";
        $type = "";
        $data = $request->all();
        if (\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores;
            $stores = [];
            foreach ($manager_stores as $store) {
                $stores[] = $store->store;
            }
        }
        $block_unblock = $data['block_unblock'] ?? '';
        if ((isset($data['store_manager']) && (trim($data['store_manager']) != "")) || (isset($data['store']) && (trim($data['store']) != "")) || (isset($data['type']) && (trim($data['type']) != "")) || (isset($data['block_unblock']) && (trim($data['block_unblock']) != ""))) {
            $contractors = Contractor::query();
            if (\Auth::user()->role == 1) {
                $contractors->where('created_by', \Auth::user()->id);
            }
            if (\Auth::user()->role == 0) {
                if (isset($data['store_manager']) && (trim($data['store_manager']) != "")) {
                    $contractors->where('created_by', $data['store_manager']);
                    $store_manager_id = $data['store_manager'];

                    $manager_stores = ManagersStore::where('manager_id', $data['store_manager'])->get()->pluck('store_id')->toArray();

                    $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
                }
            }

            if (isset($data['store']) && (trim($data['store']) != "")) {
                $contractors->where('store_id', $data['store']);
                $store_id = $data['store'];
            }

            if (isset($data['type']) && (trim($data['type']) != "")) {
                $contractors->where('type', $data['type']);
                $type = $data['type'];
            }

            if (isset($data['block_unblock']) && (trim($data['block_unblock']) != "")) {
                if (in_array($data['block_unblock'], [0, 1])) {
                    $contractors->where('block_unblock', $data['block_unblock']);
                }
            }
            $contractors = $contractors->orderBy('id', 'DESC')->paginate($per_page);
            // dd($gifts);
        } else {
            /* Without filters */
            // dd($stores);
            if (\Auth::user()->role == 0) {
                $contractors = Contractor::orderBy('id', 'DESC')->paginate($per_page);
            } else {
                $contractors = Contractor::where('created_by', \Auth::user()->id)->orderBy('id', 'DESC')->paginate($per_page);
            }
        }

        $contractor_types = config('constants.contractor_types');
        $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
        return view('admin.contractors.list', compact('contractors', 'contractor_types', 'stores', 'store_manager_id', 'store_id', 'store_managers', 'type', 'block_unblock'));
    }

    public function add()
    {
        $contractor_types = config('constants.contractor_types');
        if (\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores()->pluck('store_id')->toArray();
            $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
        }
        return view('admin.contractors.add', compact('contractor_types', 'stores'));
    }

    public function save(ContractorRequest $request)
    {
        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid.'])->withInput();
        }

        if (trim($data['generated']) == "") {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Generate a password.'])->withInput();
        }

        if (trim($data['phone']) != "") {
            $phone = (int) $data['phone'];
            if (strlen($phone) < 10) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add a valid phone number.'])->withInput();
            }
        }

        $contractor = new Contractor();
        if ($contractor->saveContractor($data)) {
            return redirect()->route('listContractors')->with(['status' => 'success', 'message' => 'Contractor added successfully.']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function edit($id)
    {
        $contractor = Contractor::find($id);
        if (!$contractor) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        $contractor_types = config('constants.contractor_types');
        if (\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores()->pluck('store_id')->toArray();
            $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();
        }
        return view('admin.contractors.edit', compact('contractor', 'contractor_types', 'stores'));
    }

    public function update($id, ContractorRequest $request)
    {
        $contractor = Contractor::find($id);
        if (!$contractor) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid.'])->withInput();
        }

        if (trim($data['phone']) != "") {
            $phone = (int) $data['phone'];
            if (strlen($phone) < 10) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add a valid phone number.'])->withInput();
            }
        }
        if ($contractor->saveContractor($data)) {
            return redirect()->route('listContractors')->with(['status' => 'success', 'message' => 'Contractor updated successfully.']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function delete($id)
    {
        $contractor = Contractor::find($id);
        if (!$contractor) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        if ($contractor->delete()) {
            return redirect()->route('listContractors')->with(['status' => 'success', 'message' => 'Contractor deleted successfully.']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
    }

    public function blockUnblock(Request $request)
    {
        $data = $request->all();
        $contractor = Contractor::find($data['id']);
        if ($contractor && (\Auth::user()->role_id == 0)) {
            if ($contractor->block_unblock == 0) {
                $contractor->block_unblock = 1;
                $msg = "Store manager blocked.";
            } else {
                $contractor->block_unblock = 0;
                $msg = "Store manager set to unblocked.";
            }
            if ($contractor->save()) {
                return response()->json([
                    "success" => 1,
                    "message" => $msg
                ]);
            } else {
                return response()->json([
                    "success" => 0,
                    "message" => 'Something went wrong, try again.'
                ]);
            }
        } else {
            return response()->json([
                "success" => 0,
                "message" => 'Invalid input.'
            ]);
        }
    }

    public function view($id)
    {
        $contractor = Contractor::find($id);
        if (!$contractor) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        if ((\Auth::user()->role != 0) && ($contractor->created_by != \Auth::user()->id)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Unauthorized.']);
        }
        return view('admin.contractors.view', compact('contractor'));
    }

    public function resetPassword(Request $request)
    {
        $data = $request->all();
        $contractor = Contractor::find($data['id']);
        if ($contractor) {
            if ((\Auth::user()->role != 0) && ($contractor->created_by != \Auth::user()->id)) {
                return response()->json([
                    "success" => 0,
                    "message" => 'Action not allowed.'
                ]);
            }
            $password = Helper::generatePassword();

            $contractor->password = bcrypt($password);
            if ($contractor->save()) {
                event(new ContractorRegistered($contractor, $password));
                return response()->json([
                    "success" => 1,
                    "message" => "Password updated successfully.",
                    "passsword" => $password
                ]);
            } else {
                return response()->json([
                    "success" => 0,
                    "message" => 'Something went wrong, try again.'
                ]);
            }
        } else {
            return response()->json([
                "success" => 0,
                "message" => 'Invalid input.'
            ]);
        }
    }


    public function export()
    {
        if (\Auth::user()->role == 0) {
            $users = Contractor::where('block_unblock', 0)->get();
        } else {
            $users = Contractor::where('created_by', \Auth::user()->id)->where('block_unblock', 0)->get();
        }
        $fileName = time() . '_appusers.csv';
        // dd($users->toArray());
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Email', 'Phone', 'Address', 'Account Type', 'Total Points');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['ID']  = $user->id;
                $row['Name']    = $user->name;
                $row['Email']    = $user->email;
                $row['Phone']  = $user->phone;
                $row['Address']  = $user->address;
                $row['AccountType']  = $user->type_name;
                $row['TotalPoints']  = $user->total_points;

                fputcsv($file, array($row['ID'], $row['Name'], $row['Email'], $row['Phone'], $row['Address'], $row['AccountType'], $row['TotalPoints']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
