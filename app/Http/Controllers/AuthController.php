<?php

namespace App\Http\Controllers;

use App\Helpers\CMail;
use App\Models\User;
use App\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        $data = [
            'pageTitle' => 'login',
        ];

        return view('back.pages.auth.login', $data);
    }

    public function loginHandler(Request $request)
    {
        // checking wheather the login is an email or a username
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required',
            ], [
                'login_id.required' => 'Enter your email or username',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'No account found for this email',
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required',
            ], [
                'login_id.required' => 'Enter your email or username',
                'login_id.exists' => 'No account found for this username',
            ]);
        }

        $creds = [
            $fieldType => $request->login_id,
            'password' => $request->password,
        ];

        if (Auth::attempt($creds)) {
            // Check if account is inactive mode
            if (auth()->user()->status == UserStatus::Inactive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->with('fail', 'Your account is currently inactive. Please, contact support at (support@larablog.test) for further assistance.');
            }

            // check if the account is in Pending mode
            if (auth()->user()->status == UserStatus::Pending) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->with('fail', 'Your account is currently pending approval. Please, check your email for futher instructions or contact support at (support@larablog.test) for assistance.');
            }

            // redirect the user to dashboard
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->withInput()->with('fail', 'Incorrect password.');
        }
    }

    public function forgotForm(Request $request)
    {
        $data = [
            'pageTitle' => 'Forgot Password',
        ];

        return view('back.pages.auth.forgot', $data);
    }

    public function sendPasswordResetLink(Request $request)
    {
        // validate the form
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Enter your email',
            'email.email' => 'Invalid email address',
            'email.exists' => 'No account found with this email address',
        ]);

        // Get the user details
        $user = User::where('email', $request->email)->first();

        // Generate Token
        $token = base64_encode(Str::random(64));

        // Check is there is an existing token
        $oldToken = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if ($oldToken) {
            // update existing token
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]);
        } else {
            // Add new reset password token
            DB::table('password_reset_tokens')
                ->insert([
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]);
        }

        // create clickable action link
        $actionLink = route('admin.reset_password_form', ['token' => $token]);

        $data = [
            'actionlink' => $actionLink,
            'user' => $user,
        ];

        $mail_body = view('email-templates.forget-template', $data)->render();

        $mailConfig = [
            'recipent_address' => $user->email,
            'recipent_name' => $user->name,
            'subject' => 'Reset Password',
            'body' => $mail_body,
        ];

        if (CMail::send($mailConfig)) {
            return redirect()->route('admin.forgot')->with('success', 'We have e-mailed your password reset link.');
        } else {
            return redirect()->route('admin.forgot')->with('fail', 'Something went wrong. Ressetting password link not sent. Try again later.');
        }

    }

    public function resetForm(Request $request, $token = null)
    {
        // check if this token exists
        $isTokenExists = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (! $isTokenExists) {
            return redirect()->route('admin.forgot')->with('fail', 'Invalid token. Request another reset password link.');
        } else {
           //check if token is not expired (15 minutes)
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $isTokenExists->created_at)->diffInMinutes(Carbon::now());

            if($diffMins > 15) {
                return redirect()->route('admin.forgot')->with('fail', 'Token expired. Request another reset password link.');
            }
            $data = [
                'pageTitle' => 'Reset Password',
                'token' => $token,
            ];

            return view('back.pages.auth.reset', $data);
        }
    }

    public function resetPasswordHandler(Request $request)
    {
        // valiate the form
        $request->validate([
            'new_password' => 'required|min:5|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required',
        ]);

        $dbToken = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        // Get User details
        $user = User::where('email', $dbToken->email)->first();

        // update password
        User::where('email', $user->email)
            ->update([
                'password' => Hash::make($request->new_password),
            ]);

        // send notification email to teh user email address that contains the new password
        $data = [
            'user' => $user,
            'new_password' => $request->new_password,
        ];

        $mail_body = view('email-templates.password-changes-template', $data);

        $mailConfig = [
            'recipent_address' => $user->email,
            'recipent_name' => $user->name,
            'subject' => 'Reset Password',
            'body' => $mail_body,
        ];

        if (CMail::send($mailConfig)) {
            // Delete the token from DB
            DB::table('password_reset_tokens')
                ->where([
                    'email' => $dbToken->email,
                    'token' => $dbToken->token,
                ])
                ->delete();

            return redirect()->route('admin.login')->with('success', 'Done!, Your password has been changed successfully. Use your new password to login into the systtem');
        } else {
            return redirect()->route('admin.reset_password_form', ['token' => $dbToken->token])->with('fail', 'Something went wrong. Try again later.');
        }

    }
}
