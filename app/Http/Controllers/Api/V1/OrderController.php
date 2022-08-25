<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contractor;
use App\Models\Order;
use App\Models\OrderGift;
use App\Models\Point;
use App\Models\RedeemRequest;
use App\Models\Bank;
use App\Models\Reference;
use App\Models\Notification;
use DB;

class OrderController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/checkout",
     * operationId="Checkout",
     * tags={"Checkout APIs"},
     * summary="Checkout",
     * description="Place order for gifts added in cart, deduct contractor points and add points redeemed history.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={ "gifts" },
     *               @OA\Property(
     *                   property="gifts", type="array",
     *                   @OA\Items(
     *                       @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=2
     *                      ),
     *                      @OA\Property(
     *                         property="points",
     *                         type="integer",
     *                         example=2000
     *                      ),
     *                   ),
     *               ),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Order placed successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function createOrder(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $messages = [
            'gifts.*.id.required' => 'The gift id is required.',
            'gifts.*.id.exists' => 'The gift id is invalid.',
            'gifts.*.points.required' => 'The gifts points is required.',
        ];
        $validator = Validator::make($request->all(), [
            'gifts' => 'required|array|min:1',
            'gifts.*.id' => 'required|exists:gifts,id',
            'gifts.*.points' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first(),
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $data = $request->all();
        $total_points = 0;
        foreach ($data['gifts'] as $gift) {
            $total_points = $total_points + $gift['points'];
        }

        if ($total_points > $contractor->total_points) {
            $res = [
                'success' => false,
                'message' => 'Your point are not sufficient for gifts.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        DB::beginTransaction();
        $order = new Order();
        $order->contractor_id = $contractor->id;
        $order->total_points = $total_points;
        $order->status = 0;
        if ($order->save()) {
            foreach ($data['gifts'] as $gift) {
                $orderGift = new OrderGift();
                $orderGift->order_id = $order->id;
                $orderGift->gift_id = $gift['id'];
                $orderGift->qty = 1;
                $orderGift->points = $gift['points'];
                $orderGift->save();
            }
            // $previos_points = $contractor->total_points;
            // $contractor->total_points = $previos_points - $total_points;
            // if ($contractor->save()) {
            $redeemRequest = new RedeemRequest();
            $redeemRequest->contractor_id = $contractor->id;
            $redeemRequest->points = $total_points;
            $redeemRequest->req_type = 3; // Gift Orders
            $redeemRequest->req_id = $order->id;
            $redeemRequest->req_status = 0;
            if ($redeemRequest->save()) {
                $point = new Point();
                $point->contractor_id = $contractor->id;
                $point->redeem = 1;
                $point->points = $total_points;
                $point->redeem_type = 3; // gifts
                $point->redeem_id = $order->id;
                $point->redeem_req_id = $redeemRequest->id;
                if ($point->save()) {
                    DB::commit();
                    return response()->json([
                        "success" => true,
                        "message" => 'Order placed successfully.',
                        'blocked' => $contractor->block_unblock
                    ]);
                }
            }
            // }
        }
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong, try again.',
            'blocked' => $contractor->block_unblock
        ], 422);
    }

    /**
     * @OA\Post(
     * path="/api/points/redeem",
     * operationId="PointsRedeem",
     * tags={"Points APIs"},
     * summary="Add request to redeem points by cash or by bank account",
     * description="Place order for gifts added in cart, deduct contractor points and add points redeemed history. request_type (1 OR 2). If 2 then provide bank_id that belongs to user.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={ "request_type", "points" },
     *               @OA\Property(property="request_type", type="integer"),
     *               @OA\Property(property="points", type="integer"),
     *               @OA\Property(property="bank_id", type="integer")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Redeem request added successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Your points are not sufficient for this redeem request.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function addRedeemRequest(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'request_type' => 'required|integer|in:1,2', // 1=>Cash,2=>Account
            'bank_id' => 'required_if:request_type,2|exists:banks,id',
            'points' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first(),
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $data = $request->all();
        if ($data['points'] > $contractor->total_points) {
            $res = [
                'success' => false,
                'message' => 'Your points are not sufficient for this redeem request.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }

        // Check bank id belongs to logged in user
        if (isset($data['bank_id']) && $data['bank_id'] != "") {
            $bank = Bank::find($data['bank_id']);
            if (!$bank || ($bank->contractor_id != $contractor->id)) {
                $res = [
                    'success' => false,
                    'message' => 'Invalid bank.',
                    'blocked' => $contractor->block_unblock
                ];
                return response()->json($res, 422);
            }
        }
        DB::beginTransaction();
        $redeem_req = new RedeemRequest();
        $redeem_req->contractor_id = $contractor->id;
        $redeem_req->points = $data['points'];
        $redeem_req->req_type = $data['request_type'];
        if ($data['request_type'] == 2) {
            $redeem_req->req_id = $data['bank_id'] ?? null;
        }
        $redeem_req->req_status = 0;
        if ($redeem_req->save()) {
            // $previos_points = $contractor->total_points;
            // $contractor->total_points = $previos_points - $data['points'];
            // if ($contractor->save()) {
            $point = new Point();
            $point->contractor_id = $contractor->id;
            $point->redeem = 1;
            $point->points = $data['points'];
            $point->redeem_req_id = $redeem_req->id;
            $point->redeem_type = $data['request_type']; // gifts
            if ($data['request_type'] == 2) {
                $point->redeem_id = $data['bank_id'];
            }
            if ($point->save()) {
                DB::commit();
                return response()->json([
                    "success" => true,
                    "message" => 'Redeem request added successfully.',
                    'blocked' => $contractor->block_unblock,
                    'total_points' => $contractor->total_points
                ]);
            }
            // }
        }
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong, try again.',
            'blocked' => $contractor->block_unblock
        ], 422);
    }

    /**
     * @OA\Post(
     * path="/api/points/history",
     * operationId="PointsHistory",
     * tags={"Points APIs"},
     * summary="Get points history",
     * description="Get points history.",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Points history found.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function getPointsHistory(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable'
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first(),
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $limit = $request->limit ? $request->limit : 10;
        $point_history = Point::select(['id', 'redeem', 'points', 'redeem_type', 'created_at', 'redeem_status', 'completed_at', 'reference_id'])->where('contractor_id', $contractor->id)->orderBy('id', 'DESC')->paginate($limit);
        if ($point_history->count() > 0) {
            $msg = "Points history found.";
        } else {
            $msg = "No points history found.";
        }
        $point_history = $point_history->toArray();
        return response()->json([
            "success" => true,
            "message" => $msg,
            'data' => $point_history['data'],
            'blocked' => $contractor->block_unblock,
            'current_page' => $point_history['current_page'],
            'last_page' => $point_history['last_page'],
            'total_results' => $point_history['total']
        ]);
    }

    /**
     * @OA\Get(
     *  path="/api/points/history/details/{id}",
     *  operationId="pointsHistoryDetails",
     *  tags={"Points APIs"},
     *  summary="Points history details.",
     *  description="Provides details of how the points were credited or redeemed.",
     *  @OA\Parameter(
     *       name="id",
     *       description="Redeem history id",
     *       required=true,
     *       in="path",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Details found.",
     *       @OA\JsonContent()
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function getPointHistoryDetails($id)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $point_history = Point::select(['id', 'redeem', 'points', 'redeem_type', 'redeem_id', 'created_at', 'redeem_status', 'completed_at', 'reference_id'])->find($id);
        if ($point_history) {
            $details = null;
            if ($point_history->redeem_type == 1) {
                $redeemed_by = 'By Cash';
            } elseif ($point_history->redeem_type == 2) {
                $redeemed_by = 'By Bank';
                $bank_details = Bank::select(['id', 'account_holder_name', 'account_number', 'bank_name', 'ifsc_code', 'account_type'])->find($point_history->redeem_id);
                if ($bank_details) {
                    $details = $bank_details->toArray();
                } else {
                    $bank_details = Bank::select(['id', 'account_holder_name', 'account_number', 'bank_name', 'ifsc_code', 'account_type'])->withTrashed()->find($point_history->redeem_id);
                    if ($bank_details) {
                        $details = $bank_details->toArray();
                    }
                }
            } elseif ($point_history->redeem_type == 3) {
                $redeemed_by = 'By Gift';
                $order = Order::with(['order_gifts', 'order_gifts.gift'])->select(['id', 'total_points', 'status'])->find($point_history->redeem_id);
                if ($order) {
                    $gifts = [];
                    foreach ($order->order_gifts as $ogift) {
                        $gift = [];
                        $gift['points'] = (int) $ogift['points'];
                        $gift['id'] = (int) $ogift['gift']['id'];
                        $gift['name'] = $ogift['gift']['name'];
                        $gift['photo_url'] = $ogift['gift']['photo_url'];
                        $gifts[] = $gift;
                    }
                    $details = $gifts;
                }
            } else {
                $redeemed_by = 'Credited';
            }
            $history_details = $point_history->toArray();
            $history_details['redeemed_by'] = $redeemed_by;
            if ($point_history->redeem_type == 1) {
                $history_details['details'] = $details;
            } elseif ($point_history->redeem_type == 2) {
                $history_details['bank_detail'] = $details;
            } elseif ($point_history->redeem_type == 3) {
                $history_details['gifts'] = $details;
            } else {
                $history_details['details'] = $details;
            }
            if ($point_history->redeem == 0) {
                $reference = Reference::find($point_history->reference_id);
                if ($reference) {
                    $history_details['reference_details'] = [
                        'full_name' => $reference->full_name,
                        'phone_number' => $reference->phone_number,
                        'email' => ($reference->email != "") ? $reference->email : 'NA',
                        'building_type' => $reference->building_type,
                        'state' => $reference->state,
                        'city' => $reference->city,
                        'address' => $reference->address,
                        'landmark' => ($reference->landmark != "") ? $reference->landmark : 'NA',
                        'area_in_sqft' => $reference->area_in_sqft,
                        'frames_count' => $reference->frames_count,
                        'status' => $reference->status,
                        'id' => $reference->id,
                        'points' => $reference->point->points ?? 0
                    ];
                }
            }
            if ($point_history->redeem == 0) {
                Notification::where('type_id', $point_history->reference_id)->update(['read' => 1]);
            } else {

                Notification::where('type_id', $point_history->id)->update(['read' => 1]);
            }

            $msg = "Details found.";
        } else {
            $history_details = [];
            $msg = "No details found.";
        }
        return response()->json([
            "success" => true,
            "message" => $msg,
            'data' => $history_details,
            'blocked' => $contractor->block_unblock
        ]);
    }
}
