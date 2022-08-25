<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;
use App\Models\ManagersStore;
use App\Models\Point;
use App\Models\Contractor;
use App\Models\Notification;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\Store;
use App\Helpers\Helper;
use DB;

class ReferenceController extends Controller
{
    public function list(Request $request)
    {
        $per_page = config('constants.admin.per_page');
        $references_statuses = config('constants.references_statuses');
        $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
        $data = $request->all();
        $store_manager_id = $data['store_manager'] ?? '';
        $store_id = $data['store'] ?? '';
        $status_filter = $data['status'] ?? '';

        $date = new \DateTime();
        $date->modify('- 1 month');
        $from_date = $date->format('Y-m-d');
        $to_date = date('Y-m-d');
        $data = $request->all();

        // Filters start
        if ((isset($data['store_manager']) && (trim($data['store_manager']) != "")) || (isset($data['store']) && (trim($data['store']) != "")) || (isset($data['status']) && (trim($data['status']) != "")) || (isset($data['from_date']) && (trim($data['from_date']) != "")) || (isset($data['to_date']) && (trim($data['to_date']) != ""))) {

            $from_date = $data['from_date'] ?? '';
            $to_date = $data['to_date'] ?? '';

            $reference = Reference::query();
            if (\Auth::user()->role == 1) {
                $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
                // echo "Here: ".\Auth::user()->id ; dd($managerContractors);
                $reference->whereIn('created_by', $managerContractors);
            }
            // dd($data);
            if (isset($data['store_manager']) && (trim($data['store_manager']) != "")) {
                $managerContractors = Contractor::where('created_by', $data['store_manager'])->get()->pluck('id')->toArray();

                $reference->whereIn('created_by', $managerContractors);
                // echo "<pre>"; print_r($reference->get()->pluck('id')); die;

            }

            if ((isset($data['store']) && (trim($data['store']) != ""))) {
                $storeContractors = Contractor::where('store_id', $data['store'])->pluck('id')->toArray();
                $reference->whereIn('created_by', $storeContractors);
            }

            if ((isset($data['status']) && (trim($data['status']) != ""))) {
                if (in_array($data['status'], $references_statuses)) {
                    $reference->where('status', $data['status']);
                }
            }

            if ((isset($data['from_date']) && (trim($data['from_date']) != ""))) {
                $from = date('Y-m-d', strtotime($data['from_date']));
                $reference->whereDate('created_at', '>=', $from);
            }

            if ((isset($data['to_date']) && (trim($data['to_date']) != ""))) {
                $to = date('Y-m-d', strtotime($data['to_date']));
                $reference->whereDate('created_at', '<=', $to);
            }

            $references = $reference->orderBy('id', 'DESC')->paginate($per_page);
            /* Filters end */
        } else {
            if (\Auth::user()->role == 0) {
                $references = Reference::orderBy('id', 'DESC')->paginate($per_page);
            } else {
                $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
                // echo "Here: ".\Auth::user()->id ; dd($managerContractors);
                $references = Reference::whereIn('created_by', $managerContractors)->orderBy('id', 'DESC')->paginate($per_page);
                // echo $references->total(); die;
            }
        }

        if (\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores;
            $stores = [];
            foreach ($manager_stores as $store) {
                $stores[] = $store->store;
            }
        }

        return view('admin.references.list', compact('references', 'store_managers', 'store_manager_id', 'store_id', 'from_date', 'to_date', 'status_filter', 'stores', 'references_statuses'));
    }

    public function view($id)
    {
        $reference = Reference::find($id);
        if (!$reference) {
            return redirect()->route('listReferences')->with(['status' => 'danger', 'message' => 'Invalid reference ID.']);
        }
        if (\Auth::user()->role != 0) {
            $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
            if (!in_array($reference->created_by, $managerContractors)) {
                return redirect()->route('listReferences')->with([
                    "status" => 'danger',
                    "message" => 'Unauthorized action.'
                ]);
            }
        }
        return view('admin.references.view', compact('reference'));
    }

    public function changeStatus(Request $request)
    {
        $data = $request->all();
        $reference = Reference::find($data['id']);
        if ($reference) {
            if (\Auth::user()->role != 0) {
                $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
                if (!in_array($reference->created_by, $managerContractors)) {
                    return response()->json([
                        "success" => 0,
                        "message" => 'Unauthorized action.'
                    ]);
                }
            } else {
                return response()->json([
                    "success" => 0,
                    "message" => 'Unauthorized action.'
                ]);
            }
            DB::beginTransaction();
            // DB::commit();
            // DB::rollback();
            $reference->status = $data['status'];
            if ($reference->save()) {
                $default_points = config('constants.acceptance_point');
                $notification = new Notification();
                if ($data['status'] == 'In Progress') {
                    $noti_text = 'Congratulations! Your ' . $reference->full_name . ' reference is accepted. You earned ' . $default_points . ' points for it.';
                    $push_message = 'Great work! Your ' . $reference->full_name . ' reference is accepted. You earned ' . $default_points . ' points for it.';
                } else {
                    $noti_text = 'Your ' . $reference->full_name . ' reference is rejected.';
                    $push_message = 'Sorry! Your ' . $reference->full_name . ' reference is rejected.';
                }
                $notification->contractor_id = $reference->created_by;
                $notification->text = $noti_text;
                $notification->type = 'reference';
                $notification->type_id = $reference->id;

                if ($notification->save()) {
                    $contractor = Contractor::find($reference->created_by);

                    if ($contractor->push_notifications == 1) {
                        $device_tokens = DeviceToken::where('user_id', $contractor->id)->orderBy('id', 'DESC')->get();
                        if ($device_tokens) {
                            foreach ($device_tokens as $device_token) {
                                $res = Helper::sendNotification($reference->id, $device_token->device_type, $device_token->device_token, $push_message, 'reference');
                            }
                            if ($res == 1) {
                                if ($data['status'] == 'In Progress') {
                                    if ($this->addDefaultPoints($reference)) {
                                        DB::commit();
                                        return response()->json([
                                            "success" => 1,
                                            "message" => "Reference status set to " . $data['status']
                                        ]);
                                    } else {
                                        DB::rollback();
                                        return response()->json([
                                            "success" => 0,
                                            "message" => "Problem while Adding points."
                                        ]);
                                    }
                                } else { // 'Rejected'
                                    DB::commit();
                                    return response()->json([
                                        "success" => 1,
                                        "message" => "Reference status set to " . $data['status']
                                    ]);
                                }
                            } else {
                                DB::rollback();
                                return response()->json([
                                    "success" => 0,
                                    "message" => "Problem while sending notification, Please try again."
                                ]);
                            }
                        }
                    }
                    if ($this->addDefaultPoints($reference)) {
                        DB::commit();
                        return response()->json([
                            "success" => 1,
                            "message" => "Reference status set to " . $data['status']
                        ]);
                    } else {
                        DB::rollback();
                        return response()->json([
                            "success" => 0,
                            "message" => "Problem while Adding points."
                        ]);
                    }
                }
            }
        } else {
            return response()->json([
                "success" => 0,
                "message" => 'Invalid input.'
            ]);
        }
        return response()->json([
            "success" => 0,
            "message" => 'Something went wrong, try again.'
        ]);
    }

    protected function addDefaultPoints($reference)
    {

        // Add default points to contractor
        $default_points = config('constants.acceptance_point');
        // $point = Point::where('reference_id', $reference->id)->first();
        // if(!$point) {
        $point = new Point();
        // }
        $point->contractor_id = $reference->created_by;
        $point->redeem = 0; // credited points
        $point->points = $default_points;
        $point->added_by = \Auth::user()->id;
        $point->reference_id = $reference->id;
        if ($point->save()) {
            $contractor = Contractor::find($reference->created_by);
            if ($contractor) {
                $old_points = $contractor->total_points;
                $contractor->total_points = $old_points + $default_points;
                if ($contractor->save()) {
                    return true;
                }
            }
        } else {
            return false;
        }
        // End default points to contractor

    }

    public function addPoints(Request $request)
    {
        $data = $request->all();
        $reference = Reference::find($data['id']);
        if ($reference) {
            if (\Auth::user()->role != 0) {
                // $manager_stores = ManagersStore::where('manager_id', \Auth::user()->id)->get()->pluck('store_id')->toArray();
                $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
                if (!in_array($reference->created_by, $managerContractors)) {
                    return response()->json([
                        "success" => 0,
                        "message" => 'Unauthorized action.'
                    ]);
                }
            }
            $data['points'] = (float) $data['points'];
            if ($data['points'] < 1) {
                return response()->json([
                    "success" => 0,
                    "message" => 'Invalid points!'
                ]);
            }
            DB::beginTransaction();
            $point = Point::where('reference_id', $reference->id)->first();
            if (!$point) {
                $point = new Point();
                $point->points = round($data['points'], 2);
            } else {
                $pts = $point->points + $data['points'];
                $point->points = round($pts, 2);
            }
            $point->contractor_id = $reference->created_by;
            $point->redeem = 0; // credited points

            $point->added_by = \Auth::user()->id;
            $point->reference_id = $reference->id;
            if ($point->save()) {
                $reference->status = 'Completed';
                if ($reference->save()) {
                    $contractor = Contractor::find($reference->created_by);
                    if ($contractor) {
                        $old_points = $contractor->total_points;
                        $contractor->total_points = $old_points + $data['points'];
                        if ($contractor->save()) {
                            $noti_text = 'You earned ' . $data['points'] . ' points for sharing the reference of ' . $reference->full_name . '.';

                            $notification = new Notification();
                            $notification->contractor_id = $reference->created_by;
                            $notification->text = $noti_text;
                            $notification->type = 'reference';
                            $notification->type_id = $reference->id;
                            if ($notification->save()) {

                                if ($contractor->push_notifications == 1) {
                                    $push_message = 'Congratulations! You earned ' . $data['points'] . ' points for sharing the reference of ' . $reference->full_name . '.';
                                    $device_tokens = DeviceToken::where('user_id', $contractor->id)->orderBy('id', 'DESC')->get();
                                    if ($device_tokens) {
                                        foreach ($device_tokens as $device_token) {
                                            $res = Helper::sendNotification($reference->id, $device_token->device_type, $device_token->device_token, $push_message, 'reference');
                                        }


                                        if ($res == 1) {
                                            DB::commit();
                                            return response()->json([
                                                "success" => 1,
                                                "message" => "Points added successfully."
                                            ]);
                                        } else {
                                            DB::rollback();
                                            return response()->json([
                                                "success" => 0,
                                                "message" => "Problem while sending notification, Please try again."
                                            ]);
                                        }
                                    }
                                }
                                DB::commit();
                                return response()->json([
                                    "success" => 1,
                                    "message" => "Points added successfully."
                                ]);
                            }
                        }
                    }
                }
            } else {
                DB::rollback();
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
}
