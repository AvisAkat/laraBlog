<?php

use Livewire\Component;
use App\Models\GeneralSettings;

new class extends Component {

    //for tabs to remain at current position after refreshing
    public $tab = null;
    public $tabname = 'general_settings';
    protected $queryString = ['tab' => ['keep' => true]];

    //General Settings form properties
    public $site_title, $site_email, $site_phone, $site_meta_keywords, $site_meta_description;


    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    //GENERAL SETTINGS
    public function updateSiteInfo()
    {
        $this->validate([
            'site_title' => 'required|min:2',
            'site_email' => 'required|email',
        ]);

        $settings = GeneralSettings::take(1)->first();

        $data = array(
            'site_title' => $this->site_title,
            'site_email' => $this->site_email,
            'site_phone' => $this->site_phone,
            'site_meta_keywords' => $this->site_meta_keywords,
            'site_meta_description' => $this->site_meta_description
        );

        if (!is_null($settings)) {
            $query = $settings->update($data);
        } else {
            $query = GeneralSettings::insert($data);
        }

        if ($query) {
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'General settings have been updated successfully!']);
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function mount()
    {
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        //Populate General Settings
        $settings = GeneralSettings::take(1)->first();

        if (!is_null($settings)) {
            $this->site_title = $settings->site_title;
            $this->site_email = $settings->site_email;
            $this->site_phone = $settings->site_phone;
            $this->site_meta_keywords = $settings->site_meta_keywords;
            $this->site_meta_description = $settings->site_meta_description;
        }
    }
};
?>

<div>
    <div class="tab">
        <ul class="nav nav-tabs customtab" role="tablist">
            <li class="nav-item">
                <a wire:click="selectTab('general_settings')"
                    class="nav-link {{ $tab == 'general_settings' ? 'active' : '' }}" data-toggle="tab"
                    href="#general_settings" role="tab" aria-selected="true">
                    General Settings
                </a>
            </li>
            <li class="nav-item">
                <a wire:click="selectTab('logo_favicon')" class="nav-link {{ $tab == 'logo_favicon' ? 'active' : ''  }}"
                    data-toggle="tab" href="#logo_favicon" role="tab" aria-selected="false">
                    Logo & Favicon
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade {{ $tab == 'general_settings' ? 'active show' : '' }}" id="general_settings"
                role="tabpanel">
                <div class="pd-20">
                    <form wire:submit="updateSiteInfo()">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><b>Site Title</b></label>
                                    <input type="text" class="form-control" wire:model="site_title"
                                        placeholder="Enter site title">
                                    @error('site_title')
                                        <span class="text-danger ml-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><b>Site Email</b></label>
                                    <input type="text" class="form-control" wire:model="site_email"
                                        placeholder="Enter site email">
                                    @error('site_email')
                                        <span class="text-danger ml-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><b>Site Phone Number</b><small> (Optional)</small></label>
                                    <input type="text" class="form-control" wire:model="site_phone"
                                        placeholder="Enter site contact number">
                                    @error('site_phone')
                                        <span class="text-danger ml-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><b>Site Meta Keywords</b><small> (Optional)</small></label>
                                    <input type="text" class="form-control" wire:model="site_meta_keywords"
                                        placeholder="Eg. ecommerce, free api, laravel 12, livewire">
                                    @error('site_meta_keywords')
                                        <span class="text-danger ml-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""><b>Site Meta Description</b><small> (Optional)</small></label>
                            <textarea wire:model="site_meta_description" class="form-control" cols="4" rows="4"
                                placeholder="type meta description..."></textarea>
                            @error('site_meta_description')
                                <span class="text-danger ml-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade {{ $tab == 'logo_favicon' ? 'active show' : '' }}" id="logo_favicon"
                role="tabpanel">
                <div class="pd-20">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Site logo</h6>
                            <div class="mb-2 mt-1" style="max-width: 200px">
                                <img wire:ignore src="/images/site/{{ isset(settings()->site_logo) ? settings()->site_logo : '' }}" alt="" class="img-thumbnail" id="preview_site_logo">
                            </div>
                            <form action="{{ route('admin.update_logo') }}" method="post" enctype="multipart/form-data"
                                id="updateLogoForm">
                                @csrf
                                <div class="mb-4">
                                    <input type="file" name="site_logo" id="site_logo"
                                        class="form-control-file form-control height-auto ">
                                    <span class="text-danger ml-1"></span>

                                </div>
                                <button type="submit" class="btn btn-primary">Change Logo</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>