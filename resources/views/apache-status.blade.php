<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apache Server Status</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="container">
        <h1>Apache Server Status</h1>
        <p class="min-connections">Showing IPs with at least {{ $min_connections }} connections.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2>Using Livewire component:</h2>
        <livewire:status-table-controller :ips="$ips" :min_connections="$min_connections" />

    </div>


    @livewireScripts
</body>
</html>
