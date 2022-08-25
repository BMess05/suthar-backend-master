<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Contractor;
use Illuminate\Support\Facades\Validator;
use Mail;

class ForgotPasswordController extends Controller
{
    protected $jwtAuth;
    function __construct( JWTAuth $jwtAuth ) {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['forgot_password', 'update_password', 'verifyOtp']]);
        //
    }

    /**
    * @OA\Post(
    * path="/api/auth/forgot_password",
    * operationId="authForgotPassword",
    * tags={"Auth APIs"},
    * summary="Forgot Password",
    * description="Forgot Password",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email"},
    *               @OA\Property(property="email", type="email")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=200,
    *          description="Otp sent on your register email.",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    function forgot_password(Request $request) {
        $messages = [
            'email.exists' => 'The entered email is not registered.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:contractors,email'
        ], $messages);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res, 422);
        }

        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Invalid email.'], 422);
        }

        $contractor = Contractor::where('email', $data['email'])->first();
        if(!$contractor) {
            return response()->json(['success' => false, 'message' => "Email not registered."], 422);
        }

        $otp = $this->generateOtp();
        $details = [
            'title' => 'OTP for password reset',
            'otp' => $otp
        ];

        $res = Contractor::where('email', $data['email'])->update(['otp' => $otp]);
        \Mail::to($data['email'])->send(new \App\Mail\ForgetPasswordMail($details));

        return response()->json(['success' => true, 'message' => 'Otp sent on your register email.', 'otp' => $otp]);

    }

    function generateOtp() {
        $otp = rand ( 100000 , 999999 );
        $res = $this->checkOTPUnique($otp);
        if($res) {
            return $otp;
        }   else {
            $this->generateOtp();
        }
    }

    function checkOTPUnique($otp) {
        $res = Contractor::where('otp', $otp)->first();
        if($res) {
            return false;
        }   else {
            return true;
        }
    }


    /**
    * @OA\Post(
    * path="/api/auth/verify_otp",
    * operationId="authVerifyOTP",
    * tags={"Auth APIs"},
    * summary="Verify OTP",
    * description="Verify OTP",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email", "otp"},
    *               @OA\Property(property="email", type="email"),
    *               @OA\Property(property="otp", type="integer"),
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=422,
    *          description="Invalid OTP.",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="OTP verified.",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found")
    * )
    */

    function verifyOtp(Request $request) {
        $messages = [
            'email.exists' => 'The entered email is invalid.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:contractors,email',
            'otp' => 'required'
        ], $messages);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res, 422);
        }

        $data = $request->all();
        $contractor = Contractor::select('otp')->where('email', $data['email'])->first();
        if(!$contractor) {
            $res = [
                'success' => false,
                'message' => "Email not registered."
            ];
            return response()->json($res, 422);
        }
        if($contractor->otp == $data['otp']) {
            $res = [
                'success' => true,
                'message' => "OTP verified."
            ];
            return response()->json($res);
        }   else {
            $res = [
                'success' => false,
                'message' => "Invalid OTP."
            ];
            return response()->json($res, 422);
        }
    }


    /**
    * @OA\Post(
    * path="/api/auth/update_password",
    * operationId="authUpdatePassword",
    * tags={"Auth APIs"},
    * summary="Verify OTP",
    * description="Verify OTP",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email", "password", "otp"},
    *               @OA\Property(property="email", type="email"),
    *               @OA\Property(property="password", type="password"),
    *               @OA\Property(property="otp", type="integer")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=422,
    *          description="invalid OTP.",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Password updated successfully.",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    function update_password(Request $request) {
        $messages = [
            'email.exists' => 'The entered email is invalid.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:contractors,email',
            'password' => 'required|string|min:6|max:20',
            'otp' => 'required'
        ], $messages);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res, 422);
        }

        $data = $request->all();
        $res = Contractor::where('otp', $data['otp'])->where('email', $data['email'])->first();

        if($res) {
            $password = bcrypt($data['password']);
            $res = Contractor::where('otp', $data['otp'])->where('email', $data['email'])->update(['password' => $password, 'otp' => null]);
            if($res) {
                return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
            }   else {
                return response()->json(['success' => false, 'message' => 'Something went wrong.'], 422);
            }
        }   else {
            return response()->json(['success' => false, 'message' => 'Invalid OTP.'], 422);
        }
    }
}
