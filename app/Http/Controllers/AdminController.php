<?php

namespace App\Http\Controllers;

use App\Models\GeneralSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
// use SawaStacks\Utils\Kropify;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function adminDashboard(Request $request)
    {
        $data = [
            'pageTitle' => 'Dashboard',
        ];

        return view('back.pages.dashboard', $data);
    }

    public function logoutHandler(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You are now logged out.');
    }

    public function profileView(Request $request)
    {
        $data = [
            'pageTitle' => 'Profile',
        ];

        return view('back.pages.profile', $data);
    }

    public function updateProfilePicture(Request $request)
    {

        $user = User::findOrFail(auth()->id());
        $path = 'images/users/';
        $file = $request->file('profilePictureFile');
        $old_picture = $user->getAttributes()['picture'];
        $filename = 'IMG_'.uniqid().'.png';

        $upload = Kropify::getFile($file, $filename)->setPath($path)->save();

        if ($upload) {
            // Delete old profile picture if exists
            if ($old_picture != null && File::exists(public_path($path.$old_picture))) {
                File::delete(public_path($path.$old_picture));
            }

            // Update Profile picture in DB
            $user->update(['picture' => $filename]);

            return response()->json([
                'status' => 1,
                'message' => 'Your profile picture has been updated successfully.',
            ]);
        } else {

            return response()->json([
                'status' => 0,
                'message' => 'Upload failed.',
            ]);
        }

    }

    public function generalSettings(Request $request)
    {
        $data = [
            'pageTitle' => 'General settings',
        ];

        return view('back.pages.general_settings', $data);

    }

    public function updateLogo(Request $request)
    {

        $settings = GeneralSettings::take(1)->first();

        if (! is_null($settings)) {
            $path = 'images/site/';
            $old_logo = $settings->site_logo;
            $file = $request->file('site_logo');
            $filename = 'logo_'.uniqid().'.png';

            if ($request->hasFile('site_logo')) {
                $upload = $file->move(public_path($path), $filename);

                if ($upload) {
                    if ($old_logo != null && File::exists(public_path($path.$old_logo))) {
                        File::delete(public_path($path.$old_logo));
                    }

                    $settings->update(['site_logo' => $filename]);

                    return response()->json(['status' => 1, 'image_path' => $path.$filename, 'message' => 'Site logo has been updated successfully.']);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Something went wrong in uploading new logo']);
                }

            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Make sure you updated general settings form first']);
        }
    }

    public function updateFavicon(Request $request)
    {
        $settings = GeneralSettings::take(1)->first();

        if (! is_null($settings)) {
            $path = 'images/site/';
            $old_favicon = $settings->site_favicon;
            $file = $request->file('site_favicon');
            $filename = 'favicon'.uniqid().'.png';

            if ($request->hasFile('site_favicon')) {
                $upload = $file->move(public_path($path), $filename);

                if ($upload) {
                    if ($old_favicon != null && File::exists(public_path($path.$old_favicon))) {
                        File::delete(public_path($path.$old_favicon));
                    }

                    $settings->update(['site_favicon'=> $filename]);

                    return response()->json(['status'=> 1,'image_path'=> $path.$filename, 'message'=> 'Site favicon has been updated successfully.']);
                }else{
                    return response()->json(['status'=> 0, 'message'=> 'Something went wrong in uploading new favicon']);
                }
            }
        }else{
            return response()->json(['status'=> 0, 'message'=> 'Make sure you updated general settings form first']);
        }
    }

    //Categories Page
    public function categoriesPage() {
        $data = [
            'pageTitle' => 'Categories'
        ];

        return view('back.pages.categories_page', $data);
    }
}
