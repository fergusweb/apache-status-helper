<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ApacheStatusService;
use App\Services\LookupIP;

/**
 * Livewire Component
 */
class StatusTable extends Component
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
     * @param LookupIP $lookupIP        Lookup class
     * @param array    $ips             Array of IP addresses
     * @param int      $min_connections Minimum connections per IP to show
     *
     * @return void
     */
    public function mount(LookupIP $lookupIP, $ips = array(), $min_connections=5)
    {
        $this->ips = $ips;
        $this->min_connections = $min_connections;

        //dd($this->ips);

        foreach ($this->ips as $key => $ip) {

            //$this->ips[$key] = $lookupIP->lookup($ip);
        }
        //dd($this->ips);
    }

    /**
     * Render
     *
     * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
     */
    public function render()
    {
        //dd($this->ips);
        return view(
            'livewire.status-table', [
                'ips'             => $this->ips,
                'min_connections' => $this->min_connections
            ]
        );
    }


    /**
     * Perform lookup when requested by LiveWire
     *
     * @param LookupIP $lookupIP Service class
     * @param string   $key      Array key requested by LiveWire
     *
     * @return void
     */
    public function lookup(LookupIP $lookupIP, $key=null)
    {
        $row = $this->ips[$key];

        $this->ips[$key]->provider = $lookupIP->provider($row->address);
        $this->ips[$key]->country = $lookupIP->country($row->address);
        $this->ips[$key]->countryCode = $lookupIP->countryCode($row->address);
    }



}
