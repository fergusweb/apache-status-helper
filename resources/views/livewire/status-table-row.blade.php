<div>
    <tr wire:key="{{ $key }}">
        <td>{{ $ip->ip }}</td>
        <td>{{ $ip->count }}</td>
        <td>
            <button type="button" wire:click="lookup({{ $key }})">Lookup</button>
        </td>
        <td>{{ $ip->provider }}</td>
    </tr>
</div>
