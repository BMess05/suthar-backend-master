<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RedeemRequest;
use App\Models\Point;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\Contractor;
use App\Models\ManagersStore;
use DB;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Helpers\Helper;

class OrderController extends Controller
{
    public function listRedeenRequests(Request $request)
    {
        $per_page = config('constants.admin.per_page');
        if (\Auth::user()->role == 0) {
            $stores = Store::select(['id', 'name', 'city'])->get();
        } else {
            $manager_stores = \Auth::user()->manager_stores;
            $stores = [];
            foreach ($manager_stores as $store) {
                $stores[] = $store->store;
            }
        }
        $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
        $redeemReq = RedeemRequest::query();
        $redeemReq->with(['bank' => function ($q) {
            $q->withTrashed();
        }]);
        if (\Auth::user()->role == 1) {
            $managerContractors = Contractor::where('created_by', \Auth::user()->id)->get()->pluck('id')->toArray();
            $redeemReq->whereIn('contractor_id', $managerContractors);
        }

        $data = $request->all();
        $store_manager_id = $data['store_manager'] ?? '';
        $store_id = $data['store'] ?? '';
        $status = $data['status'] ?? '';
        $type = $data['type'] ?? '';
        if ((isset($data['store_manager']) && (trim($data['store_manager']) != "")) || (isset($data['store']) && (trim($data['store']) != "")) || (isset($data['status']) && (trim($data['status']) != "")) || (isset($data['type']) && (trim($data['type']) != ""))) {
            if (\Auth::user()->role == 0) {
                if ((isset($data['store_manager']) && (trim($data['store_manager']) != ""))) {
                    $managerContractors = Contractor::where('created_by', $data['store_manager'])->get()->pluck('id')->toArray();

                    $manager_stores = ManagersStore::where('manager_id', $data['store_manager'])->get()->pluck('store_id')->toArray();

                    $stores = Store::select(['id', 'name', 'city'])->whereIn('id', $manager_stores)->get();

                    $redeemReq->whereIn('contractor_id', $managerContractors);
                }
            }

            if ((isset($data['store']) && (trim($data['store']) != ""))) {
                $managerContractors = Contractor::where('store_id', $data['store'])->get()->pluck('id')->toArray();
                $redeemReq->whereIn('contractor_id', $managerContractors);
            }

            if ((isset($data['status']) && (trim($data['status']) != ""))) {
                if (in_array($data['status'], [0, 1])) {
                    $redeemReq->where('req_status', $data['status']);
                };
            }

            if ((isset($data['type']) && (trim($data['type']) != ""))) {
                if (in_array($data['type'], [1, 2, 3])) {
                    $redeemReq->where('req_type', $data['type']);
                };
            }
        }
        $redeemReqs = $redeemReq->orderBy('id', 'DESC')->paginate($per_page);
        // dd($redeemReqs->toArray());
        // echo $type;
        // die;
        return view('admin.redeem_reqs.list', compact('redeemReqs', 'store_manager_id', 'store_id', 'status', 'type', 'stores', 'store_managers'));
    }

    public function changeStatusRedeemRequest(Request $request)
    {
        $data = $request->all();
        $redeemReq = RedeemRequest::find($data['id']);
        if ($redeemReq) {
            $contractor = Contractor::find($redeemReq->contractor_id);
            if (!$contractor) {
                return response()->json([
                    "success" => 0,
                    "message" => 'Contractor account not found.'
                ]);
            }
            if ($redeemReq->points > $contractor->total_points) {
                $res = [
                    'success' => 0,
                    'message' => 'User points are not sufficient for this redeem request.'
                ];
                return response()->json($res);
            }

            DB::beginTransaction();
            $previos_points = $contractor->total_points;
            $contractor->total_points = $previos_points - $redeemReq->points;
            if ($contractor->save()) {
                $point = Point::where('redeem_req_id', $redeemReq->id)->first();
                if (!$point) {
                    $res = [
                        'success' => 0,
                        'message' => 'Redeem requests history not added in Points.'
                    ];
                    return response()->json($res);
                }
                $point->redeem_status = 1;
                $point->completed_at = date('Y-m-d');
                if ($point->save()) {
                    $redeemReq->req_status = 1;
                    if ($redeemReq->save()) {

                        $notification = new Notification();
                        if ($redeemReq->req_type == 1) {
                            $noti_text = "Your cash redeem request has been processed and " . $redeemReq->points . " points are debited from your wallet.";
                        } elseif ($redeemReq->req_type == 2) {
                            $noti_text = "Your point redeem request to bank account has been processed and " . $redeemReq->points . " points are debited from your wallet.";
                        } else {
                            $noti_text = 'Your gift redeem request has been processed and ' . $redeemReq->points . ' points are debited from your wallet.';
                        }


                        $notification->contractor_id = $redeemReq->contractor_id;
                        $notification->text = $noti_text;
                        $notification->type = 'redeem_request';
                        $notification->type_id = $point->id;

                        if ($notification->save()) {
                            if ($contractor->push_notifications == 1) {
                                $device_tokens = DeviceToken::where('user_id', $contractor->id)->orderBy('id', 'DESC')->get();
                                if ($device_tokens) {
                                    // $push_message = "Your redeem request is processed  and ' . $redeemReq->points . ' points are deducted from your wallet.";
                                    foreach ($device_tokens as $device_token) {
                                        $res = Helper::sendNotification($point->id, $device_token->device_type, $device_token->device_token, $noti_text, 'redeem_request');
                                    }

                                    if ($res == 1) {
                                        DB::commit();
                                        return response()->json([
                                            "success" => 1,
                                            "message" => "Redeem request completed."
                                        ]);
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

                        DB::commit();
                        return response()->json([
                            "success" => 1,
                            "message" => "Redeem request completed."
                        ]);
                    } else {
                        DB::rollback();
                        return response()->json([
                            "success" => 0,
                            "message" => 'Something went wrong, try again.'
                        ]);
                    }
                }
            }
        }
        return response()->json([
            "success" => 0,
            "message" => 'Invalid input.'
        ]);
    }

    public function revertStatusRedeemRequest(Request $request)
    {
        $data = $request->all();
        $redeemReq = RedeemRequest::find($data['id']);
        if ($redeemReq) {
            $contractor = Contractor::find($redeemReq->contractor_id);
            if (!$contractor) {
                return response()->json([
                    "success" => 0,
                    "message" => 'Contractor account not found.'
                ]);
            }
            DB::beginTransaction();
            $previos_points = $contractor->total_points;
            $contractor->total_points = $previos_points + $redeemReq->points;
            if ($contractor->save()) {
                $point = Point::where('redeem_req_id', $redeemReq->id)->first();
                if (!$point) {
                    return response()->json([
                        "success" => 0,
                        "message" => 'Redeem request id not updated in Points.'
                    ]);
                }
                $point->redeem_status = 0;
                $point->completed_at = NULL;
                if ($point->save()) {
                    $redeemReq->req_status = 0;
                    if ($redeemReq->save()) {
                        DB::commit();
                        return response()->json([
                            "success" => 1,
                            "message" => "Redeem request set to pending."
                        ]);
                    } else {
                        DB::rollback();
                        return response()->json([
                            "success" => 0,
                            "message" => 'Something went wrong, try again.'
                        ]);
                    }
                }
            }
        }
        return response()->json([
            "success" => 0,
            "message" => 'Invalid input.'
        ]);
    }

    public function pointHistory(Request $request)
    {
        $data = $request->all();
        $per_page = config('constants.admin.per_page');
        $point = Point::query();
        if (\Auth::user()->role == 1) {
            $point->where('added_by', \Auth::user()->id);
        }
        $store_manager_id = "";
        $from_date = "";
        $to_date = "";
        $type = "";
        $date = new \DateTime();
        $date->modify('- 1 month');
        $from_date = $date->format('Y-m-d');
        $to_date = date('Y-m-d');
        if (\Auth::user()->role == 0) {
            // dd($data);
            if (isset($data['store_manager']) && (trim($data['store_manager']) != "")) {
                $store_manager_id = $data['store_manager'];
                $store_manager = User::find($data['store_manager']);
                if ($store_manager) {
                    $point->where('added_by', $data['store_manager']);
                }
            }
        }

        if (isset($data['from_date']) && (trim($data['from_date']) != "")) {
            $from_date = date('Y-m-d', strtotime($data['from_date']));
            $point->whereDate('created_at', '>=', $from_date);
        }
        if (isset($data['to_date']) && (trim($data['to_date']) != "")) {
            $to_date = date('Y-m-d', strtotime($data['to_date']));
            $point->whereDate('created_at', '<=', $to_date);
        }
        if (isset($data['type']) && ($data['type'] != "") && in_array($data['type'], [0, 1])) {
            $type = $data['type'];
            $point->where('redeem', $data['type']);
        }
        $point_history = $point->orderBy('id', 'DESC')->paginate($per_page);
        $store_managers = User::select(['id', 'name'])->where('role', 1)->get();
        return view('admin.redeem_reqs.points_history', compact('point_history', 'store_managers', 'store_manager_id', 'from_date', 'to_date', 'type'));
    }
}
