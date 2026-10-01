<?php

use Livewire\Component;
use App\Models\NewsletterSubscriber;

new class extends Component {
    public $email = '';

    //Define Validation rules
    protected $rules = [
        'email' => 'required|email|unique:newsletter_subscribers,email'
    ];

    //Define Custom error message
    protected function mmessage()
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already subscribed.'
        ];
    }

    //Real-time Validation method
    public function updatedEmail()
    {
        $this->validateOnly('email');
    }

    public function subscribe()
    {
        //Validate the email before processing the subscription
        $this->validate();

        //Save email into Database
        $created = NewsletterSubscriber::create(['email' => $this->email]);

        if ($created) {

            //Clear input and notify the user
            $this->email = '';
            $this->dispatch('showAlert', ['type' => 'success', 'message' => 'You have successfully subscribed!']);
        }else{
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong. Please Try Again Later.']);
        }
    }
};
?>

<div>
    <div class="sidebar-card" style="background: var(--gradient); border: none; color: #fff">
        <h3 style="color: #fff; border-bottom-color: rgba(255, 255, 255, 0.2)">
            📬 Newsletter
        </h3>
        <p style="font-size: 0.88rem; opacity: 0.85; margin-bottom: 16px">
            Get weekly insights delivered to your inbox.
        </p>
        <form wire:submit="subscribe()" method="POST">
            <x-form-alerts></x-form-alerts>
            <div>

                <input type="email" placeholder="Your email" wire:model.live="email" style="
                                        width: 100%;
                                        padding: 12px 16px;
                                        border: 2px solid rgba(255, 255, 255, 0.3);
                                        border-radius: 10px;
                                        background: rgba(255, 255, 255, 0.15);
                                        color: #fff;
                                        font-size: 0.9rem;
                                        outline: none;
                                        " />
                @error('email')
                    <span class="text-danger ml-1" style="font-size: 0.87rem">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" style="
                                        width: 100%;
                                        padding: 12px;
                                        background: #fff;
                                        color: var(--primary);
                                        border: none;
                                        border-radius: 10px;
                                        font-weight: 700;
                                        font-size: 0.9rem;
                                        margin-top: 12px;
                                        cursor: pointer;
                                        transition: all 0.3s;
                                      ">
                Subscribe
            </button>
        </form>
    </div>
</div>