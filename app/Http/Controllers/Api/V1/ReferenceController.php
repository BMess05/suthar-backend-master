<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Reference;
use App\Models\Contractor;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;

class ReferenceController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/stores",
     * operationId="stores",
     * tags={"References APIs"},
     * summary="List stores",
     * description="List stores",
     *      @OA\Response(
     *          response=200,
     *          description="List of stores found.",
     *          @OA\JsonContent()
     *       ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function listStores(Request $request)
    {

        $stores = Store::select(['id', 'name', 'city'])->where('is_active', 1)->get()->toArray();
        $contractor = $this->getContractor();
        $res = [
            'success' => true,
            'message' => 'List of stores found.',
            'data' => $stores,
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }

    /**
     * @OA\Get(
     *    path="/api/references/statuses",
     *    operationId="RefrencesStatuses",
     *    tags={"References APIs"},
     *    summary="List references possible statuses",
     *    description="List references possible statuses",
     *    @OA\Response(
     *        response=200,
     *        description="List of references statuses.",
     *        @OA\JsonContent()
     *    ),
     *    security={{ "apiAuth": {} }}
     * )
     */
    public function listStatuses(Request $request)
    {
        $data = config('constants.references_statuses');
        $contractor = $this->getContractor();
        $res = [
            'success' => true,
            'message' => 'List of references statuses.',
            'data' => $data,
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }
    /**
     * @OA\Post(
     * path="/api/references/add",
     * operationId="references",
     * tags={"References APIs"},
     * summary="Add Reference",
     * description="Add Reference",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"full_name" , "phone_number" , "email" , "building_type" , "state" , "city" , "address" , "area_in_sqft" , "frames_count" , "store_id"},
     *               @OA\Property(property="full_name", type="text"),
     *               @OA\Property(property="phone_number", type="text"),
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="building_type", type="text"),
     *               @OA\Property(property="state", type="text"),
     *               @OA\Property(property="city", type="text"),
     *               @OA\Property(property="address", type="text"),
     *               @OA\Property(property="landmark", type="text"),
     *               @OA\Property(property="area_in_sqft", type="integer"),
     *               @OA\Property(property="frames_count", type="integer"),
     *               @OA\Property(property="store_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Reference added successfully.",
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
    public function addReference(Request $request)
    {
        $contractor = $this->getContractor();
        $building_types = config('constants.building_types');
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|min:3|max:50',
            'phone_number' => 'required|digits:10',
            'email' => 'nullable|email',
            'building_type' => 'required|in:' . implode(',', $building_types),
            'state' => 'required|string',
            'city' => 'required|string',
            'address' => 'required',
            'landmark' => 'sometimes',
            'area_in_sqft' => 'required|numeric',
            'frames_count' => 'required|integer',
            // 'store_id' => 'required|exists:stores,id'
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
        if (isset($data['email']) && (trim($data['email']) != "")) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return response()->json(['success' => false, 'message' => 'Email Address is invalid', 'blocked' => $contractor->block_unblock], 422);
            }
        }


        $reference = new Reference();
        if ($reference->saveReference($data)) {
            return response()->json(['success' => true, 'message' => 'Reference added successfully.', 'blocked' => $contractor->block_unblock]);
        }
        return response()->json(['success' => false, 'message' => 'Could not add reference, please try again.', 'blocked' => $contractor->block_unblock], 422);
    }

    /**
     * @OA\Post(
     * path="/api/references",
     * operationId="ReferencesList",
     * tags={"References APIs"},
     * summary="References List",
     * description="References List",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="status", type="array",
     *                   @OA\Items(
     *                       @OA\Property(
     *                         property="Pending",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="In Progress",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="Rejected",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="Accepted",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="All",
     *                         type="string",
     *                         example=""
     *                      ),
     *                   ),
     *               ),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="List of references found.",
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
    public function listReferences(Request $request)
    {
        $contractor = $this->getContractor();
        $validator = Validator::make($request->all(), [
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable',
            'status' => 'sometimes|array'
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
        $data = $request->all();
        $reference = Reference::query();
        $reference->select(['id', 'full_name', 'city', 'status'])->where('created_by', auth('api')->user()->id);
        if (isset($data['status']) && (count($data['status']) > 0)) {
            if ($data['status'][0] != "All") {
                $reference->whereIn('status', $data['status']);
            }
        }
        if (isset($data['date']['from']) && ($data['date']['from'] != null)) {
            $reference->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime($data['date']['from'])));
        }
        if (isset($data['date']['to']) && ($data['date']['to'] != null)) {
            $reference->whereDate('created_at', '<=', date('Y-m-d H:i:s', strtotime($data['date']['to'])));
        }

        $references = $reference->orderBy('id', 'DESC')->paginate($limit)->toArray();

        if (count($references) > 0) {
            $msg = "List of references found.";
        } else {
            $msg = "Don't have any reference yet.";
        }
        $unread_notifications_count = Notification::where('contractor_id', $contractor->id)->where('read', 0)->count();
        // configuartion point value
        $point_to_rs = config('constants.point');
        $res = [
            'success' => true,
            'message' => $msg,
            'data' => $references['data'],
            'blocked' => $contractor->block_unblock,
            'current_page' => $references['current_page'],
            'last_page' => $references['last_page'],
            'total_results' => $references['total'],
            'unread_notification_count' => $unread_notifications_count,
            'point_to_rs' => $point_to_rs
        ];
        return response()->json($res);
    }


    /**
     * @OA\Post(
     *   path="/api/references/update/{id}",
     *   operationId="updateReference",
     *   tags={"References APIs"},
     *   summary="Update Reference",
     *   description="Update Reference, only Pending Refrences can be updated",
     *   @OA\Parameter(
     *       name="id",
     *       description="Reference id",
     *       required=true,
     *       in="path",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"full_name" , "phone_number" , "email" , "building_type" , "state" , "city" , "address" , "area_in_sqft" , "frames_count" , "store_id"},
     *               @OA\Property(property="full_name", type="text"),
     *               @OA\Property(property="phone_number", type="text"),
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="building_type", type="text"),
     *               @OA\Property(property="state", type="text"),
     *               @OA\Property(property="city", type="text"),
     *               @OA\Property(property="address", type="text"),
     *               @OA\Property(property="landmark", type="text"),
     *               @OA\Property(property="area_in_sqft", type="integer"),
     *               @OA\Property(property="frames_count", type="integer"),
     *               @OA\Property(property="store_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Reference added successfully.",
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
    public function updateReference($id, Request $request)
    {
        $contractor = $this->getContractor();
        $reference = Reference::find($id);
        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Invalid reference ID.', 'blocked' => $contractor->block_unblock], 422);
        }

        if ($reference->status != "Pending") {
            return response()->json(['success' => false, 'message' => 'Action not allowed, Only Pending References can be updated.', 'blocked' => $contractor->block_unblock], 422);
        }
        $building_types = config('constants.building_types');
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|min:3|max:50',
            'phone_number' => 'required|digits:10',
            'email' => 'nullable|email',
            'building_type' => 'required|in:' . implode(',', $building_types),
            'state' => 'required|string',
            'city' => 'required|string',
            'address' => 'required',
            'landmark' => 'sometimes',
            'area_in_sqft' => 'required|numeric',
            'frames_count' => 'required|integer',
            // 'store_id' => 'required|exists:stores,id'
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
        if (isset($data['email']) && (trim($data['email']) != "")) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return response()->json(['success' => false, 'message' => 'Email Address is invalid', 'blocked' => $contractor->block_unblock], 422);
            }
        }

        if ($reference->saveReference($data)) {
            return response()->json(['success' => true, 'message' => 'Reference updated successfully.', 'blocked' => $contractor->block_unblock]);
        }
        return response()->json(['success' => false, 'message' => 'Could not update reference, please try again.', 'blocked' => $contractor->block_unblock], 422);
    }

    /**
     * @OA\Get(
     *   path="/api/references/delete/{id}",
     *   operationId="deleteReference",
     *   tags={"References APIs"},
     *   summary="Delete Reference",
     *   description="Delete Reference",
     *   @OA\Parameter(
     *       name="id",
     *       description="Reference id",
     *       required=true,
     *       in="path",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Reference deleted successfully.",
     *          @OA\JsonContent()
     *       ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function deleteReference($id)
    {
        $contractor = $this->getContractor();
        $reference = Reference::find($id);
        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Invalid reference ID.', 'blocked' => $contractor->block_unblock], 422);
        }

        if ($reference->status != "Pending") {
            return response()->json(['success' => false, 'message' => 'Action not allowed, Only Pending References can be deleted.', 'blocked' => $contractor->block_unblock], 422);
        }

        if ($reference->delete()) {
            return response()->json(['success' => true, 'message' => 'Reference deleted successfully.', 'blocked' => $contractor->block_unblock]);
        }
        return response()->json(['success' => false, 'message' => 'Could not delete reference, please try again.', 'blocked' => $contractor->block_unblock], 422);
    }

    /**
     * @OA\Get(
     *  path="/api/references/details/{id}",
     *  operationId="referenceDetails",
     *  tags={"References APIs"},
     *  summary="Delete Reference",
     *  description="Delete Reference",
     *  @OA\Parameter(
     *       name="id",
     *       description="Reference id",
     *       required=true,
     *       in="path",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Reference deleted successfully.",
     *       @OA\JsonContent()
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     */
    public function referenceDetails($id)
    {
        $contractor = $this->getContractor();
        $reference = Reference::find($id);
        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Invalid reference ID.', 'blocked' => $contractor->block_unblock], 422);
        }
        $data = [
            'full_name' => $reference->full_name,
            'phone_number' => $reference->phone_number,
            'email' => ($reference->email != "") ? $reference->email : 'NA',
            'building_type' => $reference->building_type,
            'state' => $reference->state,
            'city' => $reference->city,
            'address' => $reference->address,
            'landmark' => ($reference->landmark != "") ? $reference->landmark : 'NA',
            'area_in_sqft' => (int) $reference->area_in_sqft,
            'frames_count' => (int) $reference->frames_count,
            // 'store_location' => $reference->store->city ?? '',
            'status' => $reference->status,
            'id' => (int) $reference->id,
            'points' => $reference->point ? $reference->point->points : 0,
            // 'store_id' => (int) $reference->store_id
        ];
        return response()->json(['success' => true, 'message' => 'Reference details found.', 'data' => $data, 'blocked' => $contractor->block_unblock]);
    }

    protected function getContractor()
    {
        return Contractor::find(auth('api')->user()->id);
    }
}
