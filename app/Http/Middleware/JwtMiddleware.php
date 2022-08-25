<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user = auth('api')->user();
            // dd($user);
            if( !$user ) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            } // throw new Exception('User Not Found');
        } catch (Exception $e) {

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['success' => false, 'message' => 'Token Invalid'], 403);

            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                try
                {
                    $newToken = JWTAuth::parseToken()->refresh();
                    $user = JWTAuth::setToken($newToken)->toUser();
                    $response = $next($request);
                    $response->headers->set('Authorization', 'Bearer '.$newToken);
                }
                catch (JWTException $e)
                {
                    return response()->json(['success' => false, 'message' => 'Logged Out'], 401);
                }
                // return response()->json(['success' => false, 'message' => 'token expired', 'code' => 401]);

            }   else {
                if( $e->getMessage() === 'User Not Found') {
                    return response()->json(['success' => false, 'message' => 'User not found'], 400);
                }
                return response()->json(['success' => false, 'message' => 'Authorization Token not found'], 422);
            }
        }
        return $next($request);
    }
}
