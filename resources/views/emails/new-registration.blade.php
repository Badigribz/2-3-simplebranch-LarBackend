<!DOCTYPE html>
<html>
<body>
    <h2>New Registration Request</h2>

    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Who are they?</strong></p>
    <blockquote>{{ $user->registration_note }}</blockquote>

    <p>
        <a href="http://127.0.0.1:1234/admin.html">Go to Admin Panel to Approve</a>
    </p>
</body>
</html>
