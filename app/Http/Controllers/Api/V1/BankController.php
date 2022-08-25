<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Contractor;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    /**
    * @OA\Post(
    * path="/api/banks/add",
    * operationId="AddBank",
    * tags={"Banks APIs"},
    * summary="Add Bank",
    * description="Add Bank",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"account_holder_name" , "account_number" , "bank_name" , "ifsc_code" , "account_type"},
    *               @OA\Property(property="account_holder_name", type="text"),
    *               @OA\Property(property="account_number", type="text"),
    *               @OA\Property(property="bank_name", type="text"),
    *               @OA\Property(property="ifsc_code", type="text"),
    *               @OA\Property(property="account_type", type="text")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=200,
    *          description="Bank details added successfully.",
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
    public function addBank(Request $request) {
        $contractor = Contractor::find(auth('api')->user()->id);
        $validator = Validator::make($request->all(), [
            'account_holder_name' =>'required|string|min:3|max:50',
            'account_number' =>'required|string|min:8|max:20',
            'bank_name' =>'required|string|min:3|max:30',
            'ifsc_code' =>'required|string|min:8|max:20',
            'account_type' =>'required|string|min:6|max:30'
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
        $existing_bank = Bank::where('contractor_id', auth('api')->user()->id)->where('account_number', $data['account_number'])->first();
        if($existing_bank) {
            $res = [
                'success' => false,
                'message' => 'Account number already added',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        $data = $request->all();
        $bank = new Bank();
        $bank->contractor_id = auth('api')->user()->id;
        $bank->account_holder_name = $data['account_holder_name'];
        $bank->account_number = $data['account_number'];
        $bank->bank_name = $data['bank_name'];
        $bank->ifsc_code = $data['ifsc_code'];
        $bank->account_type = $data['account_type'];
        if($bank->save()) {
            $res = [
                'success' => true,
                'message' => 'Bank details added successfully.',
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
    * path="/api/banks/update/{id}",
    * operationId="UpdateBank",
    * tags={"Banks APIs"},
    * summary="Update Bank Details.",
    * description="Update Bank Details.",
    *   @OA\Parameter(
    *       name="id",
    *       description="Bank id",
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
    *               required={"account_holder_name" , "account_number" , "bank_name" , "ifsc_code" , "account_type"},
    *               @OA\Property(property="account_holder_name", type="text"),
    *               @OA\Property(property="account_number", type="text"),
    *               @OA\Property(property="bank_name", type="text"),
    *               @OA\Property(property="ifsc_code", type="text"),
    *               @OA\Property(property="account_type", type="text")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=200,
    *          description="Bank details updated successfully.",
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
    public function updateBank($id, Request $request) {
        $contractor = Contractor::find(auth('api')->user()->id);
        $bank = Bank::find($id);
        if(!$bank) {
            $res = [
                'success' => false,
                'message' => 'Invalid Bank ID.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        if(auth('api')->user()->id != $bank->contractor_id) {
            $res = [
                'success' => false,
                'message' => 'Unauthorized action.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }

        $validator = Validator::make($request->all(), [
            'account_holder_name' =>'required|string|min:3|max:50',
            'account_number' =>'required|string|min:8|max:20',
            'bank_name' =>'required|string|min:3|max:30',
            'ifsc_code' =>'required|string|min:8|max:20',
            'account_type' =>'required|string|min:6|max:30'
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
        $bank->account_holder_name = $data['account_holder_name'];
        $bank->account_number = $data['account_number'];
        $bank->bank_name = $data['bank_name'];
        $bank->ifsc_code = $data['ifsc_code'];
        $bank->account_type = $data['account_type'];
        if($bank->save()) {
            $res = [
                'success' => true,
                'message' => 'Bank details updated successfully.',
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
    * path="/api/banks",
    * operationId="BanksList",
    * tags={"Banks APIs"},
    * summary="Banks List",
    * description="Banks List",
    *
    *      @OA\Response(
    *          response=200,
    *          description="List of Banks found.",
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
    public function listBanks() {
        $contractor = Contractor::find(auth('api')->user()->id);
        $banks = Bank::select(['id', 'account_holder_name', 'account_number', 'bank_name', 'ifsc_code', 'account_type'])->where('contractor_id', auth('api')->user()->id)->get()->toArray();
        if(count($banks) > 0) {
            $msg = "List of Banks found.";
        }   else {
            $msg = "No bank details added yet.";
        }
        $res = [
            'success' => true,
            'message' => $msg,
            'data' => $banks,
            'blocked' => $contractor->block_unblock
        ];
        return response()->json($res);
    }

    /**
    * @OA\Get(
    * path="/api/banks/delete/{id}",
    * operationId="BankDelete",
    * tags={"Banks APIs"},
    * summary="Bank Delete",
    * description="Bank Delete",
    *   @OA\Parameter(
    *       name="id",
    *       description="Bank id",
    *       required=true,
    *       in="path",
    *       @OA\Schema(
    *           type="integer"
    *       )
    *   ),
    *      @OA\Response(
    *          response=200,
    *          description="Bank deleted successfully.",
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
    public function deleteBank($id) {
        $contractor = Contractor::find(auth('api')->user()->id);
        $bank = Bank::find($id);
        if(!$bank) {
            $res = [
                'success' => false,
                'message' => 'Invalid Bank ID.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        if(auth('api')->user()->id != $bank->contractor_id) {
            $res = [
                'success' => false,
                'message' => 'Unauthorized action.',
                'blocked' => $contractor->block_unblock
            ];
            return response()->json($res, 422);
        }
        if($bank->delete()) {
            $res = [
                'success' => true,
                'message' => "Bank details deleted successfully.",
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
}
