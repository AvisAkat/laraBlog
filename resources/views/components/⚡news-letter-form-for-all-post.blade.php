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
        } else {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Something went wrong. Please Try Again Later.']);
        }
    }
};
?>

<div>
    <h3>📬 Newsletter</h3>
    <p>Get weekly insights delivered to your inbox.</p>
    <form method="POST" wire:submit="subscribe()">
        <x-form-alerts></x-form-alerts>
        <div>
            <input type="email" wire:model.live="email" placeholder="Your email">
            @error('email')
                 <span class="text-danger ml-1" style="font-size: 0.87rem">{{ $message }}</span>
            @enderror
        </div>
        <button>Subscribe</button>
    </form>
</div>