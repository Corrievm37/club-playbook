<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coach Invitation</title>
</head>
<body>
    <p>Hi Coach,</p>
    <p>You have been invited by {{ $club->name ?? 'the club' }} to join as a coach.</p>
    <p>Please complete your profile (name, surname, ID, BokSmart number) and upload your qualifications using the secure link below:</p>
    <p><a href="{{ $url }}">Complete Coach Profile</a></p>
    <p>This link may expire; if it does, ask your club manager to resend it.</p>
    <p>Thank you.</p>
</body>
</html>
