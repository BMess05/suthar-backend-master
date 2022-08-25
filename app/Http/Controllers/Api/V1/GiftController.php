<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\GiftStore;
use App\Models\Contractor;
use App\Models\ManagersStore;
use Illuminate\Support\Facades\Validator;

class GiftController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/gifts",
     * operationId="giftsListing",
     * tags={"Gifts APIs"},
     * summary="List gifts",
     * description="List gifts",
     *      @OA\Response(
     *          response=200,
     *          description="List of gifts found.",
     *          @OA\JsonContent()
     *       ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function listGifts(Request $request)
    {
        $contractor = $this->getContractor();
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
        // $manager_stores = ManagersStore::where('manager_id', auth('api')->user()->created_by)->pluck('store_id')->toArray();
        // $giftStores = GiftStore::select(['gift_id'])->whereIn('store_id', $manager_stores)->orderBy('id', 'DESC')->distinct('gift_id')->paginate($limit);
        $giftStores = GiftStore::select(['id', 'gift_id'])->where('store_id', $contractor->store_id)->orderBy('id', 'DESC')->distinct('gift_id')->paginate($limit);

        $gifts_arr = [];
        foreach ($giftStores as $i => $gift_store) {
            $row = [];
            $row['id'] = (int) $gift_store->gift->id;
            $row['name'] = $gift_store->gift->name;
            $row['photo'] = $gift_store->gift->photo;
            $row['points'] = (int) $gift_store->gift->points;
            $row['photo_url'] = $gift_store->gift->photo_url;
            $gifts_arr[] = $row;
        }
        $gifts = $giftStores->toArray();

        $res = [
            'success' => true,
            'message' => 'List of gifts found.',
            'data' => $gifts_arr,
            'blocked' => (int) $contractor->block_unblock,
            'current_page' => (int) $gifts['current_page'],
            'last_page' => (int) $gifts['last_page'],
            'total_results' => (int) $gifts['total']
        ];
        return response()->json($res);
    }

    protected function getContractor()
    {
        return Contractor::find(auth('api')->user()->id);
    }

    /**
     * @OA\Post(
     * path="/api/cart",
     * operationId="Cart",
     * tags={"Checkout APIs"},
     * summary="Cart info",
     * description="Return Gifts information in response to the array if ids of gifts in cart",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"gifts"},
     *               @OA\Property(
     *                   property="gifts", type="array",
     *                   @OA\Items(
     *                       @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                      )
     *                   ),
     *               ),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="List of gifts found.",
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
    public function getCartInfo(Request $request)
    {
        $contractor = $this->getContractor();
        $validator = Validator::make($request->all(), [
            'gifts' => 'required|array|min:1'
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

        $gifts = Gift::select(['id', 'name', 'photo', 'points'])->where('is_active', 1)->whereIn('id', $data['gifts'])->get()->toArray();
        if (count($gifts) < count($data['gifts'])) {
            $msg = 'Some of gift items are not available in stock.';
            $gifts = [];
        } else {
            $msg = 'Gifts information listed.';
        }
        $res = [
            'success' => true,
            'message' => $msg,
            'data' => $gifts,
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }
}
