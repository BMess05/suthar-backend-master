<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Contractor;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $jwtAuth;
    public function __construct(JWTAuth $jwtAuth)
    {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['login', 'logout', 'refresh']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * operationId="authLogin",
     * tags={"Auth APIs"},
     * summary="User Login",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password", "device_token", "device_type"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="device_token", type="text"),
     *               @OA\Property(property="device_type", type="integer")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Login Successfully",
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_token' => 'required',
            'device_type' => 'required|in:1,2'
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res, 422);
        }

        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Email Address is invalid.'], 422);
        }


        $contractor = Contractor::where('email', $data['email'])->first();
        if (!$contractor) {
            $contractor = Contractor::where('email', $data['email'])->onlyTrashed()->first();
            if ($contractor) {
                return response()->json(['success' => false, 'message' => 'We have deleted all your data from the system. If you want to login then please create an account again. You have to use different email to login.'], 422);
            }
            return response()->json(['success' => false, 'message' => 'Email & password do not match.'], 422);
        }

        if ($contractor->block_unblock == 1) {
            return response()->json(['success' => false, 'message' => 'Your account is blocked, Please contact Store Manager.'], 422);
        }
        $credentials = $request->only('email', 'password');

        if ($token = auth('api')->attempt($credentials)) {
            $token_save = DeviceToken::updateOrCreate(['device_token' => $data['device_token'], 'device_type' => $data['device_type']], ['user_id' => $contractor->id]);

            if (!$token_save) {
                $res = ['success' => false, 'message' => 'Could not save device token.'];
                return response()->json($res, 500);
            }

            $contractor = auth('api')->user();
            Log::info('Email logged in successfully: ' . $data['email']);
            $res = [
                'success' => true,
                'message' => 'Login successfully.',
                'token' => $token,
                'data' => $contractor->toArray()
            ];
            return response()->json($res);
        }
        $res = ['success' => false, 'message' => 'Email or Password do not match.'];
        return response()->json($res, 403);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * @OA\Post(
     * path="/api/logout",
     * operationId="logout",
     * tags={"Auth APIs"},
     * summary="User Logout",
     * description="Logout User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"device_token", "device_type"},
     *               @OA\Property(property="device_token", type="text"),
     *               @OA\Property(property="device_type", type="integer")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=403,
     *          description="Token Invalid",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully logged out",
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
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required|in:1,2'
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res);
        }

        $data = $request->all();
        $user_id = auth('api')->user()->id;
        $token = $this->jwtAuth->parseToken();
        $this->jwtAuth->invalidate($token);

        $data = $request->all();

        $resp = DeviceToken::where('device_token', $data['device_token'])->where('user_id', $user_id)->delete();

        return response()->json(['success' => true, 'message' => 'Successfully logged out.']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token =  $this->jwtAuth->refresh();
        $res = [
            'success' => true,
            'message' => 'Token Refreshed',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
        return response()->json($res);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
