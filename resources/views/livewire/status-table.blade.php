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
                @php
                if (!is_array($ip) || !array_key_exists('ip', $ip)) {
                    continue;
                }
                $row_style = '';
                if (array_key_exists('company', $ip) && stripos($ip['company']['name'], 'hostdime') !== false) {
                    $row_style .= 'color:#009b15;';
                }
                @endphp


                <tr style="{{ $row_style }}">
                    <td>
                        <input id="check_{{ $key }}" type="checkbox" value="{{ $ip['ip'] }}">
                    </td>
                    <td>
                        <label for="check_{{ $key }}">
                            {{ $ip['ip'] }}
                        </label>
                    </td>
                    <td>
                        {{ $ip['count'] }}
                        @php
                        if ($ip['is_datacenter']) {
                            echo '<span title="Datacenter" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#a77e00"></i>
                                    <i class="fa-solid fa-server fa-stack-1x fa-inverse"></i>
                                </span>';
                        }
                        if ($ip['is_crawler']) {
                            echo '<span title="Proxy Detected" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#4083e7"></i>
                                    <i class="fa-solid fa-spider fa-stack-1x fa-inverse"></i>
                                </span>';
                        }
                        if ($ip['is_proxy']) {
                            echo '<span title="Proxy Detected" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#c00"></i>
                                    <i class="fa-solid fa-info fa-stack-1x fa-inverse"></i>
                                </span>';
                        }
                        if ($ip['is_vpn']) {
                            echo '<span title="VPN Detected" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#c00"></i>
                                    <i class="fa-solid fa-info fa-stack-1x fa-inverse"></i>
                                </span>';
                        }
                        if ($ip['is_abuser']) {
                            echo '<span title="Abuser Detected!" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#d36a08"></i>
                                    <i class="fa-solid fa-exclamation fa-stack-1x fa-inverse"></i>
                                </span>';
                        }
                        if ($ip['is_tor']) {
                            echo '<span title="Tor Exit NOde" class="fa-stack" style="font-size:0.6em;" >
                                    <i class="fa-solid fa-circle fa-stack-2x" style="color:#0bbaf0"></i>
                                    <i class="fa-solid fa-tornado fa-stack-1x fa-inverse"></i>
                                </span>';
                        }

                        @endphp
                    </td>
                    <td>
                        @if (array_key_exists('location', $ip) && $ip['location']['country_code'])
                            <span class="fi fi-{{ strtolower($ip['location']['country_code']) }}"></span>
                            {{ $ip['location']['country'] }}
                        @else
                            <button type="button" wire:click="lookup({{ $key }})">Lookup</button>
                        @endif
                    </td>
                    <td>
                        @php
                        $provider = 'err';
                        if (array_key_exists('company', $ip) && $ip['company']) {
                            if (array_key_exists('doman', $ip['company'])) {
                                $provider = 'Company: ' . $ip['company']['name'] . ' ('.$ip['company']['domain'].')';
                            } else {
                                $provider = 'Company: ' . $ip['company']['name'];
                            }

                        }
                        if ($ip['is_datacenter'] && $ip['datacenter']) {
                            $provider = 'Datacenter: ' . $ip['datacenter']['datacenter'];
                        }
                        @endphp
                        {{ $provider }}
                    </td>
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


    @php
    //echo '<pre>', print_r($ips, true), '</pre>';
    @endphp


</div>
