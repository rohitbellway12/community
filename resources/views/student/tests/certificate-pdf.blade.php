<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Malgun Gothic", "Apple SD Gothic Neo", sans-serif;
        }
    </style>
</head>
<body class="p-4 flex items-center justify-center min-h-screen">
    @include('student.tests.partials.certificate-card')
</body>
</html>
