<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    /**
     * Handle user login and generate token.
     */
    public function login(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        // Generate token for the user
        $token = $user->createToken("tokenName")->plainTextToken;

        // Handle remember me functionality
        if ($request->remember_me) {
            $user->remember_token = Str::random(60);
            $user->remember_token_expires_at = Carbon::now()->addDays(30);
            $user->save();
        }

        return response()->json([
            "status" => "success",
            "data" => [
                "token" => $token,
                "remember_token" => $user->remember_token // Return remember_token if set
            ]
        ], 200);
    }

    /**
     * Check the validity of the remember token.
     */
    public function checkRememberToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required|string',
        ], [
            'remember_token.required' => 'Remember token is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        // Find user by remember token
        $user = User::where('remember_token', $request->remember_token)->first();

        if (!$user || Carbon::now()->greaterThan($user->remember_token_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid or expired'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User authenticated',
            'user' => $user
        ], 200);
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'Email is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        // Create a new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful'
        ], 201);
    }

    /**
     * Handle user logout and invalidate token and remember token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete(); // Revoke current token

        // Clear remember token and expiration
        $user->remember_token = null;
        $user->remember_token_expires_at = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Generate OTP
        $otp = Str::random(6);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->otp_verified = false; // Mark OTP as not verified
        $user->save();

        // Here you would typically send the OTP via email or SMS
        // For demonstration, we return it in the response (remove in production)

        return response()->json([
            'status' => 'success',
            'message' => 'OTP generated successfully',
            'otp' => $otp
        ], 200);
    }

    // public function forgotPassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //     ], [
    //         'email.required' => 'Email is required.',
    //         'email.email' => 'Invalid email format.',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             "status" => "error",
    //             "data" => [
    //                 "errors" => $validator->errors()
    //             ]
    //         ], 422);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // Generate OTP
    //     $otp = Str::random(6);
    //     $user->otp = $otp;
    //     $user->otp_expires_at = Carbon::now()->addMinutes(10);
    //     $user->otp_verified = false; // Mark OTP as not verified
    //     $user->save();

    //     // Send OTP via email
    //     Mail::to($user->email)->send(new OtpMail($otp));

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'OTP sent successfully. Please check your email.',
    //     ], 200);
    // }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'otp.required' => 'OTP is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if ($user->otp !== $request->otp || Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Mark OTP as verified
        $user->otp_verified = true;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully',
        ], 200);
    }

    /**
     * Handle password reset request.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => [
                    "errors" => $validator->errors()
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Check if OTP is verified
        if (!$user->otp_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP must be verified before resetting the password.'
            ], 400);
        }

        // Check if OTP exists and is not expired
        if (!$user->otp || Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP not found or expired. Please request a new OTP.'
            ], 400);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        // Clear OTP fields after password reset
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->otp_verified = false;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successful'
        ], 200);
    }
}
