<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ApacheStatusService;

/**
 * Livewire Controller
 */
class StatusTableController extends Component
{

    /**
     * IP address data
     *
     * @var array
     */
    public $ips;

    /**
     * Min connections per IP to show
     *
     * @var int
     */
    public $min_connections;



    /**
     * Mount function
     *
     * @param array $ip_count        Array of connections from service class
     * @param int   $min_connections Minimum connections per IP to show
     *
     * @return void
     */
    public function mount($ips = array(), $min_connections=null)
    {
        $this->ips = $ips;
        $this->min_connections = $min_connections;
    }

    /**
     * Render
     *
     * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
     */
    public function render()
    {
        return view(
            'livewire.status-table-controller', [
                'ips'             => $this->ips,
                'min_connections' => $this->min_connections
            ]
        );
    }


    public function lookup($key=null)
    {
        dd("Looking up: $key");
        //$row = $this->ips[$ip];
        //$this->ips[$ip]->provider = 'testing';
        //$this->provider = 'testing';
    }

}
