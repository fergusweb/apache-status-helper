<div>
    <table class="connections">
        <thead>
            <tr>
                <th><i class="fa-regular fa-square"></i></th>
                <th>IP Address</th>
                <th>Count</th>
                <th>Country</th>
                <th>Provider</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ips as $key => $ip)
                <tr>
                    <td>
                        <input id="check_{{ $key }}" type="checkbox" value="{{ $ip->address }}">
                    </td>
                    <td>
                        <label for="check_{{ $key }}">
                            {{ $ip->address }}
                        </label>
                    </td>
                    <td>{{ $ip->count }}</td>
                    <td>
                        @if ($ip->country)
                            <span class="fi fi-{{ $ip->countryCode }}"></span>
                            {{ $ip->country }}
                        @else
                            <button type="button" wire:click="lookup({{ $key }})">Lookup</button>
                        @endif
                    </td>
                    <td>{{ $ip->provider }}</td>
                </tr>
            @endforeach

            @if (empty($ips))
                <tr>
                    <td colspan="4">No connections found.</td>
                </tr>
            @endif
        </tbody>
    </table>


    <div class="commands">
        <h2>Commands to temporarily ban selected IPs via CSF</h2>
        <label class="duration">
            CSF duration:
            <select id="csf_ttl">
                <option value="3600">1 hour</option>
                <option value="14400">4 hours</option>
                <option value="28800">8 hours</option>
                <option value="43200">12 hours</option>
                <option value="86400" selected>24 hours</option>
                <option value="172800">2 days</option>
                <option value="604800">7 days</option>
                <option value="1209600">14 days</option>
                <option value="2592000">30 days</option>
            </select>
        </label>
        <p class="copy">
            <button>Copy to Clipboard</button>
        </p>
        <pre id="commands">Tick some boxes...</pre>
    </div>




</div>
