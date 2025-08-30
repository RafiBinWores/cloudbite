<th scope="col" class="px-4 lg:px-6 py-3 cursor-pointer" wire:click="setSortBy('{{ $name }}')">
    {{ $displayName }}
    @if ($sortBy !== $name)
        <i class="fa-regular fa-angles-up-down ps-1"></i>
    @elseif ($sortDir === 'ASC')
        <i class="fa-regular fa-angle-up ps-1"></i>
    @else
        <i class="fa-regular fa-angle-down ps-1"></i>
    @endif
</th>
