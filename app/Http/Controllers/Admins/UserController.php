<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        $user = User::join('credentials', 'users.id', '=', 'credentials.user_id')
            ->select('users.*', 'credentials.password as pass')
            ->where('users.privilege', 6)
            ->orderBy('id', 'desc')
            ->get();

        $user->map(function($user) {
            $user->usdt = number_format($user->usdt, 3);
            $user->btc = number_format($user->btc, 3);
            $user->eth = number_format($user->eth, 3);
            $user->bnb = number_format($user->bnb, 3);

            return $user;
        });

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully!'
        ]);
    }

    public function status (Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();

        $user = User::find($validated['user_id']);

        if($user->status === 0) {
            $user->status = 1;
        }else{
            $user->status = 0;
        }

        if(!$user->save()) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        return $this->index();
    }

    public function delete (Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
                'message' => 'Please fill all fields properly!'
            ], 422);
        }


        $validated = $validator->validated();


        $user = User::find($validated['user_id']);


        try {
            DB::transaction(function() use ($user) {

                $credential = Credential::where('user_id', $user->id)->forceDelete();


                $user->forceDelete();

                if(!$user && !$credential) {
                    throw new \Exception('Error deleting user');
                }

                return $user;

            });

            return $this->index();

        }catch(QueryException $e) {
            // Handles DB errors like duplicate keys, foreign key failures, etc.
            return response()->json([
                "error" => $e->getMessage(),
                "message" => "Database error occurred. Possibly email / username already exist or invalid data."
            ], 422);
        }catch(\Exception $e) {
            return response()->json([
                "error" => 'Error',
                'message' => 'Please fill all fields properly!'
            ], 422);
        }
    }


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

    public function view($id) {


        $user = User::join('credentials', 'users.id', '=', 'credentials.user_id')
            ->select('users.*', 'credentials.password as pass')
            ->where('users.id', $id)->first();

        if(!$user) {
            return response()->json([
                "error" => "Error",
                'message' => 'Error retrieving user'
            ], 422);
        }

        return response()->json([
            'data' => $user,
            'message' => 'User laoded successfully!'
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
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

        $user = User::find($validated['user_id']);


        if(!$user) {
            return response()->json([
                "error" => "Error",
                'message' => 'Error retrieving user'
            ], 422);
        }

        try {
            $userData = DB::transaction(function() use ($user, $validated) {
                $updatedUser = $user->update([
                    'fullname' => $validated['fullname'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password']),
                ]);

                $credential = Credential::where('user_id', $user->id)->update([
                    'password' => $validated['password']
                ]);


                if(!$updatedUser && !$credential) {
                    throw new \Exception('Failed to create user');
                }

                return $updatedUser;
            });


            return response()->json([
                "data" => $userData,
                'message' => 'User updated successfully!'
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
            'message' => 'Password changed successfully!'
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
}
