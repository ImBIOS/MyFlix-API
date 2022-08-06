<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'User profile data successfully fetched');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        try {
            // Check if json or not
            if (count($request->json()->all()) > 0) {
                $request = $request->json()->all();
            } else {
                return ResponseFormatter::error([
                    'message' => 'Invalid request'
                ], 'Invalid request', 400);
            }

            $validator = Validator::make($request, [
                'email' => 'email|required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), 'Validation failed', 400);
            }

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 401);
            }

            $user = User::where('email', $request['email'])->first();
            if (!Hash::check($request['password'], $user->password, [])) {
                throw new \InvalidArgumentException('Invalid Credentials');
            }

            $tokenResult = $user->createToken('token')->plainTextToken;
            return ResponseFormatter::success([
                'token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 401);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        try {
            // Check if json or not
            if (count($request->json()->all()) > 0) {
                $request = $request->json()->all();
            } else {
                return ResponseFormatter::error([
                    'message' => 'Invalid request'
                ], 'Invalid request', 400);
            }


            $validator = Validator::make($request, [
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), 'Validation Failed', 422);
            }

            User::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'birthdate' => $request['birthdate'],
                'gender' => $request['gender'],
                'password' => Hash::make($request['password']),
            ]);

            $user = User::where('email', $request['email'])->first();

            $tokenResult = $user->createToken('token')->plainTextToken;

            return ResponseFormatter::success([
                'token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Registration Failed', 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            // Check if json or not
            if (count($request->json()->all()) > 0) {
                $request = $request->json()->all();
            } else {
                return ResponseFormatter::error([
                    'message' => 'Invalid request'
                ], 'Invalid request', 400);
            }

            $data = $request;

            $user = Auth::user();
            $user->update($data);

            return ResponseFormatter::success(['user' => $user], 'Profile Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Profile Update Failed', 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(['error' => $validator->errors()], 'Update Photo Fails', 401);
        }

        if ($request->file('file')) {

            $file = $request->file->store('assets/user', 'public');

            //store your file into database
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([$file], 'File successfully uploaded');
        }
    }
}
