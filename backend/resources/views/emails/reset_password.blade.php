<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Notification</title>
</head>

<body>
    <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>

    <p>
        <a href="{{ $url }}"
            style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            {{ __('Reset Password') }}
        </a>
    </p>

    <p>{{ __('This password reset link will expire in :count minutes.', ['count' => $expireMinutes]) }}</p>

    <p>{{ __('If you did not request a password reset, no further action is required.') }}</p>
</body>

</html>
