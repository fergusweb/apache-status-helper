<?php

namespace App\Livewire;

use Livewire\Component;


/**
 * Livewire Controller
 */
class StatusTableRow extends Component
{

    /**
     * Array Key for this row
     *
     * @var int
     */
    public $key;

    /**
     * IP Address
     *
     * @var string
     */
    public $ip;

    /**
     * Counter of connections for this IP
     *
     * @var int
     */
    public $count;

    /**
     * Country of the IP address lookup
     *
     * @var string
     */
    public $country;

    /**
     * Network provider for the IP address looup
     *
     * @var string
     */
    public $provider;



    public function render()
    {
        return view(
            'livewire.status-table-row', [
                'key'      => $this->key,
                'ip'       => $this->ip,
                'count'    => $this->count,
                'country'  => $this->country,
                'provider' => $this->provider,
            ]
        );
    }


}
