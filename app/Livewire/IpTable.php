<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class IpTable extends Component
{
    public $rows = [
        ['ip' => '8.8.8.8', 'count' => 5, 'country' => null, 'provider' => null],
        ['ip' => '1.1.1.1', 'count' => 3, 'country' => null, 'provider' => null],
        // Add more rows as needed
    ];

    public function lookup($index)
    {
        $ip = $this->rows[$index]['ip'];

        // Server-side lookup (example using a mock service or API)
        // Replace the URL with an actual IP lookup service
        $response = Http::get("https://ipinfo.io/{$ip}/json");

        if ($response->successful()) {
            $data = $response->json();
            $this->rows[$index]['country'] = $data['country'] ?? 'Unknown';
            $this->rows[$index]['provider'] = $data['org'] ?? 'Unknown';
        } else {
            $this->rows[$index]['country'] = 'Lookup failed';
            $this->rows[$index]['provider'] = 'Lookup failed';
        }
    }

    public function render()
    {
        return view('livewire.ip-table');
    }
}
