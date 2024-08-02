<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ message('verification_code_email_title', 'blade') }}</title>
</head>
<body>
<div style="font-family: Arial, sans-serif; font-size: 16px;">
    <p>{{ message('verification_code_intro', 'blade', ['type' => $type]) }}</p>
    <p>{{ message('verification_code', 'blade', ['code' => $code]) }}</p>
    <p>{{ message('verification_code_expires', 'blade', ['expires' => config('admin.mail.code_expiration')]) }}</p>
    <br>
    <p>{{ message('best_regards', 'blade') }},</p>
    <p>{{ message('the_team', 'blade') }}</p>
</div>
</body>
</html>
