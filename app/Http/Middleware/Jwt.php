<?php

namespace App\Http\Middleware;

use App\Http\Resources\ApiResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class Jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            if(!$request->hasHeader('Authorization')){
                $resource = ApiResource::message('Token not found', HttpResponse::HTTP_NOT_FOUND);
                return response()->json($resource, Response::HTTP_NOT_FOUND);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $resource = ApiResource::message('User not found', HttpResponse::HTTP_NOT_FOUND);
                return response()->json($resource, Response::HTTP_NOT_FOUND);
            }
            //code...
        } catch (TokenExpiredException $e) {
            $resource = ApiResource::message('Token has expired', Response::HTTP_UNAUTHORIZED);
            return response()->json($resource, Response::HTTP_UNAUTHORIZED);
        }catch (TokenInvalidException $e) {
            $resource = ApiResource::message('Token is invalid', Response::HTTP_UNAUTHORIZED);
            return response()->json($resource, Response::HTTP_UNAUTHORIZED);
        }catch (JWTException $e) {
            $resource = ApiResource::message('Token not fount', Response::HTTP_NOT_FOUND);
            return response()->json($resource, Response::HTTP_UNAUTHORIZED);

        }
        return $next($request);
    }
}
