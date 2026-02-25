<?php

use Livewire\Component;
use App\Models\User;
use App\Models\UserSocialLink;
use Illuminate\Http\Request;
use App\Helpers\CMail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

new class extends Component {

    protected $listeners = ['refreshUserInfo' => '$refresh'];
    public $user; //Displaying user info

    //for tabs to remain at their current position even after refreshing
    public $tab = null;
    public $tabname = 'personal_details';
    protected $queryString = ['tab' => ['keep' => true]];

    //For Personal Details Form
    public $name, $email, $username, $bio;

    //For Update Password Form
    public $current_password, $new_password, $new_password_confirmation;

    //For Social Links Form
    public $facebook_url, $youtube_url, $instagram_url, $x_url, $github_url, $linkedin_url;

    //TAB FUNCTION
    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    // PERSONAL DETAILS
    public function updatePersonalDetails()
    {
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:500',
        ]);

        //Update user details
        $user->name = $this->name;
        $user->username = $this->username;
        $user->bio = $this->bio;
        $updated = $user->save();

        sleep(1);

        if ($updated) {
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Personal details updated successfully!']);
            $this->dispatch('refreshUserInfo'); // Refresh user info in the top bar
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Failed to update personal details. Please try again.']);
        }

    }

    //UPDATE PASSWORD
    public function updatePassword()
    {
        $user = User::findOrFail(auth()->id());

        //Validate the form
        $this->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        // return $fail(__('Your current does not match our records'));
                        return $fail(('Your current does not match our records'));
                    }
                }
            ],
            'new_password' => 'required|min:5|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required|min:5'
        ]);

        //Update user details
        $updated = $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        if ($updated) {
            //send email notification 
            $data = [
                'user' => $user,
                'new_password' => $this->new_password
            ];

            $mail_body = view(
                'email-templates.password-changes-template',
                $data
            )->render();

            $mail_config = [
                'recipent_address' => $user->email,
                'recipent_name' => $user->name,
                'subject' => 'Password Changed',
                'body' => $mail_body
            ];

            CMail::send($mail_config);
            //Logout and Redirect User to login Page
            Auth::logout();
            Session::flash('info', 'Your passord has been changed successfully changed. Please login with new password.');
            $this->redirectRoute('admin.login');

        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Failed to update password. Please try again.']);
        }

    }

    //SOCIAL LINKS
    public function updateSociallinks()
    {
        $this->validate([
            'facebook_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'x_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url'
        ]);

        //Get user details
        $user = User::findOrFail(auth()->id());

        $data = array(
            'facebook_url' => $this->facebook_url,
            'youtube_url' => $this->youtube_url,
            'instagram_url' => $this->instagram_url,
            'x_url' => $this->x_url,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url
        );

        if (!is_null($user->social_links)) {

            //Update Records
            $query = $user->social_links()->update($data);
            
        } else {

            //Insert New Record
            $data['user_id'] = $user->id;
            $query = UserSocialLink::insert($data);

        }

        if ($query) {
                $this->dispatch('showAlert', ['type' => 'success', 'message' => 'Social Links Updated sucessfully!!']);
            } else {
                $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Failed to update social links. Please try again.']);
            }

    }



    public function mount()
    {
        $this->user = auth()->user();
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        //Populate Personal Details Form Colums
        $user_info = User::with('social_links')->findOrFail(auth()->id());
        $this->name = $user_info->name;
        $this->email = $user_info->email;
        $this->username = $user_info->username;
        $this->bio = $user_info->bio;

        //Populate Social Links Form Columns
        if (!is_null($user_info->social_links)) {
            $this->facebook_url = $user_info->social_links->facebook_url;
            $this->youtube_url = $user_info->social_links->youtube_url;
            $this->instagram_url = $user_info->social_links->instagram_url;
            $this->x_url = $user_info->social_links->x_url;
            $this->github_url = $user_info->social_links->github_url;
            $this->linkedin_url = $user_info->social_links->linkedin_url;

        }
    }

};


?>

<div>
    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
            <div class="pd-20 card-box height-100-p">
                <div class="profile-photo">
                    <a href="javascript:;"
                        onclick="event.preventDefault();document.getElementById('profilePictureFile').click()"
                        class="edit-avatar"><i class="fa fa-pencil"></i></a>
                    <img src="{{ $user->picture }}" alt="" class="avatar-photo" id="profilePicturePreview">
                    <input type="file" name="profilePictureFile" id="profilePictureFile" class="d-none"
                        style="opacity: 0;">
                </div>
                <h5 class="text-center h5 mb-0">{{ $user->name }}</h5>
                <p class="text-center text-muted font-14">
                    {{ $user->email }}
                </p>

                <div class="profile-social">
                    <h5 class="mb-20 h5 text-blue">Social Links</h5>
                    <ul class="clearfix">
                        <li>
                            <a href="#" class="btn" data-bgcolor="#3b5998" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(59, 89, 152);"><i
                                    class="fa fa-facebook"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#1da1f2" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(29, 161, 242);"><i
                                    class="fa fa-twitter"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#007bb5" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(0, 123, 181);"><i
                                    class="fa fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#f46f30" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(244, 111, 48);"><i
                                    class="fa fa-instagram"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#c32361" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(195, 35, 97);"><i
                                    class="fa fa-dribbble"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#3d464d" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(61, 70, 77);"><i
                                    class="fa fa-dropbox"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#db4437" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(219, 68, 55);"><i
                                    class="fa fa-google-plus"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#bd081c" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(189, 8, 28);"><i
                                    class="fa fa-pinterest-p"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#00aff0" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(0, 175, 240);"><i
                                    class="fa fa-skype"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn" data-bgcolor="#00b489" data-color="#ffffff"
                                style="color: rgb(255, 255, 255); background-color: rgb(0, 180, 137);"><i
                                    class="fa fa-vine"></i></a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
            <div class="card-box height-100-p overflow-hidden">
                <div class="profile-tab height-100-p">
                    <div class="tab height-100-p">
                        <ul class="nav nav-tabs customtab" role="tablist">
                            <li class="nav-item">
                                <a wire:click="selectTab('personal_details')"
                                    class="nav-link {{ $tab == 'personal_details' ? 'active' : '' }}" data-toggle="tab"
                                    href="#personal_details" role="tab">Personal details</a>
                            </li>
                            <li class="nav-item">
                                <a wire:click="selectTab('update_password')"
                                    class="nav-link {{ $tab == 'update_password' ? 'active' : '' }}" data-toggle="tab"
                                    href="#update_password" role="tab">Update
                                    Password</a>
                            </li>
                            <li class="nav-item">
                                <a wire:click="selectTab('social_links')"
                                    class="nav-link {{ $tab == 'social_links' ? 'active' : '' }}" data-toggle="tab"
                                    href="#social_links" role="tab">
                                    social Links</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade {{ $tab == 'personal_details' ? 'show active' : '' }}"
                                id="personal_details" role="tabpanel">
                                <div class="pd-20">
                                    <form wire:submit="updatePersonalDetails()">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Full Name</label>
                                                    <input type="text" class="form-control" wire:model="name"
                                                        placeholder="Enter full name">
                                                    @error('name')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Email</label>
                                                    <input type="text" class="form-control" wire:model="email"
                                                        placeholder="Enter email address" disabled>
                                                    @error('email')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Username</label>
                                                    <input type="text" class="form-control" wire:model="username"
                                                        placeholder="Enter Username">
                                                    @error('username')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Bio</label>
                                                    <textarea name="bio" cols="4" rows="4" class="form-control"
                                                        name="bio" placeholder="Type your bio..." id=""></textarea>
                                                    @error('bio')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" type="submit">Save Cahnges</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade {{ $tab == 'update_password' ? 'show active' : '' }}"
                                id="update_password" role="tabpanel">
                                <div class="pd-20 profile-task-wrap">
                                    <form wire:submit="updatePassword()">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Current password</label>
                                                    <input type="password" class="form-control"
                                                        wire:model="current_password"
                                                        placeholder="Enter Current password">
                                                    @error('current_password')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <label for="">New password</label>
                                                    <input type="password" class="form-control"
                                                        wire:model="new_password" placeholder="Enter New password">
                                                    @error('new_password')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <label for="">Confirm New password</label>
                                                    <input type="password" class="form-control"
                                                        wire:model="new_password_confirmation"
                                                        placeholder="Confirm New password">
                                                    @error('new_password_confirmation')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade {{ $tab == 'social_links' ? 'show active' : '' }}"
                                id="social_links" role="tabpanel">
                                <div class="pd-20 profile-task-wrap">
                                    <form wire:submit="updateSociallinks()" method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>Facebook <i class="icon-copy fa fa-facebook-official" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="facebook_url"
                                                        placeholder="Facebook URL" class="form-control">
                                                    @error('facebook_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>Instagram <i class="icon-copy fa fa-instagram" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="instagram_url"
                                                        placeholder="Instagram URL" class="form-control">
                                                    @error('instagram_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>Youtube <i class="icon-copy fa fa-youtube-play" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="youtube_url"
                                                        placeholder="Youtube URL" class="form-control">
                                                    @error('youtube_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>LinkedIn <i class="icon-copy fa fa-linkedin-square" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="linkedin_url"
                                                        placeholder="LinkedIn URL" class="form-control">
                                                    @error('linkedin_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>X <i class="icon-copy fa fa-twitter-square" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="x_url" placeholder="X URL"
                                                        class="form-control">
                                                    @error('x_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for=""><b>GitHub <i class="icon-copy fa fa-github" aria-hidden="true"></i></b></label>
                                                    <input type="text" wire:model="github_url" placeholder="GitHub URL"
                                                        class="form-control">
                                                    @error('github_url')
                                                        <span class="text-danger ml-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>