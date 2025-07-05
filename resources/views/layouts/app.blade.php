<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Modern API Tester')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @yield('head')
</head>

<body class="bg-gray-900 text-white font-sans min-h-screen px-4 py-6">
    @yield('content')
</body>

</html>