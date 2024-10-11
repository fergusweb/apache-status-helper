<div>
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
            @foreach ($ips as $key => $ip)

                <livewire:status-table-row :ip="$ip" :wire:key="$key" />
            @endforeach

            @if (empty($ips))
                <tr>
                    <td colspan="4">No connections found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
