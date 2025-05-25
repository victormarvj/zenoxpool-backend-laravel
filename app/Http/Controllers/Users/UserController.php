<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

class UserController extends Controller
{


    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'password' => 'required|confirmed|min:6'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $exists = User::where('email', $validated['email'])
            ->orWhere('username', $validated['username'])
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Error',
                'message' => 'Email or username already exists.'
            ], 409);
        }


        try {
            $userData = DB::transaction(function() use ($validated) {
                $user = User::create([
                    'fullname' => $validated['fullname'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password'])
                ]);


                $credential = Credential::create([
                    'user_id' => $user->id,
                    'password' => $validated['password'],
                ]);


                if(!$user && !$credential) {
                    throw new \Exception('Failed to create user');
                }

                return $user;
            });


            return response()->json([
                "data" => $userData,
                'message' => 'Registration successful.'
            ]);

        }catch(QueryException $e) {
            // Handles DB errors like duplicate keys, foreign key failures, etc.
            return response()->json([
                "error" => $e->getMessage(),
                "message" => "Database error occurred. Possibly email / username already exist or invalid data."
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "message" => "An error occurred. Please try again."
            ], 422);
        }

    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::where('email', $validated['email'])->first();

        if(!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                "error" => "Error",
                'message' => 'Invalid credentials'
            ], 422);
        }

        if($user->status === 0) {
            return response()->json([
                "error" => "Error",
                'message' => 'Account inactive. Please contact your account admininstrator'
            ], 422);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user,
            'message' => 'Login successful'
        ]);
    }

    public function settings(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = $request->user();

        if(!$user || !Hash::check($validated['old_password'], $user->password)) {
            return response()->json([
                "error" => "Error",
                'message' => 'Invalid old password'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'Password changed successfully!'
        ]);
    }

    public function profile(Request $request) {
        $user = $request->user();

        return response()->json([
            'data' => $user,
            'message' => 'Profile loaded successfully!'
        ]);
    }

    public function updateProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'phone' => 'required|string',
            'image' => 'string|nullable',
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();


        $user = $request->user()->update([
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone'],
            'image' => $validated['image'],
        ]);


        return response()->json([
            'data' => $user,
            'message' => 'Profile updated successfully!'
        ]);
    }


    public function imageUpload(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:2048', // Accept only images
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }

        // Handle file upload
        if (!$request->hasFile('image')) {
            return response()->json([
                "error" => 'Image file not found.',
                'message' => 'No image found'
            ], 422);
        }

        $imagePath = $request->file('image')->store('profile_pic', 'public');

        return response()->json([
            'data' => $imagePath,
            'message' => 'Image loaded successfully!'
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'data' => 'Success',
            'message' => 'Logged out'
        ]);
    }
}
