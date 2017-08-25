@component('mail::message')
    # Hello {{ $user->name }}

    Thank you for create an account. Please verify your email use the button below:

    @component('mail::button', ['url' => route('verify', $user->verification_token) ])
        Verify
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
