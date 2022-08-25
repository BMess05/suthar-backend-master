<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contractor;
use App\Models\DeviceToken;
use App\Models\Point;
use App\Models\Reference;
use App\Models\Order;
use App\Models\OrderGift;
use App\Models\RedeemRequest;
use App\Models\Notification;
use App\Models\Bank;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;
use DB;

class ContractorController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/points",
     *  operationId="totalPoints",
     *  tags={"Points APIs"},
     *  summary="Total Points",
     *  description="Get contractors total points.",
     *  @OA\Response(
     *       response=200,
     *       description="Total points.",
     *       @OA\JsonContent()
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function getTotalPoints()
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        if (!$contractor) {
            return response()->json(['success' => false, 'message' => 'Invalid input.', 'blocked' => $contractor->block_unblock], 422);
        }
        $data['total_points'] = (int) $contractor->total_points;
        $data['unread_notifications_count'] = Notification::where('contractor_id', $contractor->id)->where('read', 0)->count();
        return response()->json([
            'success' => true,
            'message' => 'Total points.',
            'data' => $data,
            'blocked' => $contractor->block_unblock
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/profile/update",
     * operationId="updateProfile",
     * tags={"Profile APIs"},
     * summary="Update profile information",
     * description="Update profile information",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name" , "phone" , "address"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="phone", type="text"),
     *               @OA\Property(property="address", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Profile updated successfully.",
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
    public function updateProfile(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'phone' => 'required|digits:10',
            'address' => 'required|string|min:3'
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
        $contractor->name = $data['name'];
        $contractor->phone = $data['phone'];
        $contractor->address = $data['address'];
        if ($contractor->save()) {
            $res = [
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $contractor->toArray(),
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res);
        }
        $res = [
            'success' => false,
            'message' => 'Something went wrong, please try again.',
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res, 422);
    }

    /**
     * @OA\Post(
     * path="/api/profile/upload_image",
     * operationId="updateProfilePic",
     * tags={"Profile APIs"},
     * summary="Update profile picture",
     * description="Update profile picture",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"profile_pic"},
     *               @OA\Property(property="profile_pic", type="file")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Image uploaded successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="The profile pic field is required.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request."),
     *      @OA\Response(response=404, description="Resource Not Found."),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function uploadProfilePic(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first(),
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }

        $file = $request->file('profile_pic');

        $nm = str_replace("-", "_", $file->getClientOriginalName());
        $nm = trim($nm);
        $nm = str_replace(" ", "_", $file->getClientOriginalName());
        $name = time() . '_' .  $nm;
        $path = public_path('/uploads/contractors');
        if (!\File::exists($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }
        $file_r = $file->move($path, $name);
        $path = url('/uploads/contractors/' . $name);

        $contractor->photo = $name;
        if ($contractor->save()) {
            $res = ['success' => true, 'message' => 'Image uploaded successfully.', 'photo_url' => $path, 'blocked' => $contractor->block_unblock];
            $code = 200;
        } else {
            $res = ['success' => true, 'message' => 'Something went wrong, Please try again.', 'blocked' => $contractor->block_unblock];
            $code = 422;
        }
        return response()->json($res, $code);
    }

    /**
     * @OA\Get(
     *  path="/api/profile",
     *  operationId="getProfile",
     *  tags={"Profile APIs"},
     *  summary="Get profile",
     *  description="Get profile details.",
     *   @OA\Response(
     *       response=200,
     *       description="Profile details found.",
     *       @OA\JsonContent()
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function getProfile()
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        if (!$contractor) {
            $res = [
                'success' => false,
                'message' => 'Details not found.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $res = [
            'success' => true,
            'message' => 'Profile details found.',
            'data' => $contractor->toArray(),
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }

    /**
     * @OA\Post(
     * path="/api/profile/change_password",
     * operationId="changePassword",
     * tags={"Profile APIs"},
     * summary="Change password",
     * description="Change password",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"old_password" , "new_password"},
     *               @OA\Property(property="old_password", type="text"),
     *               @OA\Property(property="new_password", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Password updated successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Old password does not match.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function changePassword(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|max:20'
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
        if (Hash::check($data['old_password'], $contractor->password)) {
            $contractor->password = Hash::make($data['new_password']);
            if ($contractor->save()) {
                $res = [
                    'success' => true,
                    'message' => 'Password updated successfully.',
                    'blocked' => $contractor->block_unblock
                ];
                return response()->json($res);
            }
        } else {
            $res = [
                'success' => false,
                'message' => 'Old password does not match.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $res = [
            'success' => false,
            'message' => 'Something went wrong, please try again.',
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res, 422);
    }

    /**
     * @OA\Post(
     * path="/api/profile/push_notifications",
     * operationId="pushNotificationsSettings",
     * tags={"Profile APIs"},
     * summary="Push notification settings",
     * description="Change push notification settings, send 1 to turn ON notififications and 0 for turning it off.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"notifications"},
     *               @OA\Property(property="notifications", type="integer")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Notification settings updated.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="The notifications field is required.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function pushNotifications(Request $request)
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'notifications' => 'required|integer|in:0,1'
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
        $contractor->push_notifications = $data['notifications'];
        if ($contractor->save()) {
            $res = [
                'success' => true,
                'message' => 'Notification settings updated.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res);
        }
        $res = [
            'success' => false,
            'message' => 'Something went wrong, please try again.',
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res, 422);
    }

    /**
     * @OA\Get(
     * path="/api/notifications",
     * operationId="notificationsListing",
     * tags={"Profile APIs"},
     * summary="List notifications",
     * description="List notifications",
     *      @OA\Response(
     *          response=200,
     *          description="Notifications list found.",
     *          @OA\JsonContent()
     *       ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function getNotifications(Request $request)
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

        $notifications = Notification::select(['id', 'text', 'read', 'type', 'type_id', 'created_at'])->where('contractor_id', $contractor->id)->orderBy('id', 'DESC')->paginate($limit);
        $notifications_arr = [];
        if ($notifications->count() > 0) {
            $msg = "Notifications list found.";
            $ids = $notifications->pluck('id')->toArray();
            $res = Notification::whereIn('id', $ids)->update(['read' => 1]);
            foreach ($notifications as $noti) {
                $row = [];
                $row['id'] = $noti->id;
                $row['text'] = $noti->text;
                $row['read'] = $noti->read;
                $row['type'] = $noti->type;
                $row['type_id'] = $noti->type_id;
                $row['created_at'] = $noti->created_at;
                $notifications_arr[] = $row;
            }

            $noti_data = $notifications->toArray();
        } else {
            $msg = "No notifications found.";
        }
        $res = [
            'success' => true,
            'message' => $msg,
            'data' => $notifications_arr,
            'blocked' => $contractor->block_unblock,
            'current_page' => $noti_data['current_page'] ?? 1,
            'last_page' => $noti_data['last_page'] ?? 0,
            'total_results' => $noti_data['total'] ?? 0
        ];
        return response()->json($res);
    }

    /**
     * @OA\Get(
     * path="/api/leaders-boards",
     * operationId="leadersBoardsListing",
     * tags={"Profile APIs"},
     * summary="List leadersBoard",
     * description="List leadersBoard",
     *      @OA\Response(
     *          response=200,
     *          description="LeadersBoard list found.",
     *          @OA\JsonContent()
     *       ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function leaderBoard()
    {
        $contractor = Contractor::find(auth('api')->user()->id);
        $leaderBoard = Contractor::select(['name', 'photo', 'type'])->where('total_points', '>', 0)->orderBy('total_points', 'DESC')->withCount(['points as total_points' => function ($q) {
            $q->where('redeem', 0)->select(\DB::raw('SUM(points)'));
        }])->get();

        if ($leaderBoard->count() > 0) {
            $msg = "LeadersBoard list found.";
        } else {
            $msg = "No user found.";
        }
        $res = [
            'success' => true,
            'message' => $msg,
            'data' => $leaderBoard->toArray(),
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }

    public function deleteAccount()
    {
        $contractor_id = auth('api')->user()->id;
        $contractor = Contractor::find($contractor_id);
        if (!$contractor) {
            $res = [
                'success' => false,
                'message' => "Contractor not found.",
                'blocked' => 1
            ];
            return response()->json($res, 422);
        }
        $contractor->delete();

        DeviceToken::where('user_id', $contractor_id)->delete();
        Point::where('contractor_id', $contractor_id)->delete();
        Reference::where('created_by', $contractor_id)->delete();
        $orders = Order::where('contractor_id', $contractor_id)->pluck('id');
        Order::where('contractor_id', $contractor_id)->delete();
        OrderGift::whereIn('order_id', $orders)->delete();
        RedeemRequest::where('contractor_id', $contractor_id)->delete();
        Notification::where('contractor_id', $contractor_id)->delete();
        Bank::where('contractor_id', $contractor_id)->delete();
        $res = [
            'success' => true,
            'message' => "Account deleted successfully",
            'blocked' => 1
        ];
        return response()->json($res, 200);
    }
}
