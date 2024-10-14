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

        <p class="debug">
            Showing IPs with at least {{ $min_connections }} connections,
            and caching for {{ $cache_duration }} seconds.
        </p>

        @if ($errors->any())
            <ul class="errors fa-ul">
                @foreach ($errors->all() as $error)
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-circle-exclamation"></i></span>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        @endif

        <h2>Connections</h2>
        <livewire:status-table :ips="$ips" :min_connections="$min_connections" />

        <h2 style="padding-top:2em;">Helpful Commands</h2>
        <p>This command shows all connections to your server, sorted by # connections.  Helpful for seeing if a huge spike is coming from one address.</p>
        <pre>netstat -ntu | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -n</pre>

    </div>



    @livewireScripts
</body>
</html>
