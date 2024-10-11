<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apache Server Status</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        @if (!empty($ip_count))
            <table class="table">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Count</th>
                        <th>Country</th>
                        <th>Provider</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    arsort($ip_count);
                    @endphp
                    @foreach ($ip_count as $ip => $count)
                        <tr>
                            <td>{{ $ip }}</td>
                            <td>{{ $count }}</td>
                            <td>Todo: Country</td>
                            <td>Todo: Provider</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No IP addresses found.</p>
        @endif
    </div>
</body>
</html>
