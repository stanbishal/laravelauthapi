<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;


class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request) : JsonResponse
    {
        try {
            $validatedData = $request->validated();

            if(!Auth::attempt(['email'=>$validatedData['email'] , 'password' =>$validatedData['password']])){
            return $this->error(
                message:'Email & Password does not match with our record.',
                statusCode:Response::HTTP_UNAUTHORIZED
            );
            }

            $user = User::where('email', $validatedData['email'])->first();

            // delete all others tokens from database which were not deleted due to some reaseon
            $user->tokens()->delete();

            return $this->success(
                message:'User Logged In Successfully',
                data:[
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ],
                statusCode:Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return $this->error(
                message:$exception->getMessage(),
                statusCode:Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);

            return $this->success(
                message:'User Registered Successfully',
                data:[
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ],
                statusCode:Response::HTTP_CREATED
            );

        } catch (\Exception $exception) {
            return $this->error(
                message:$exception->getMessage(),
                statusCode:Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(statusCode:Response::HTTP_NO_CONTENT);
    }

}
