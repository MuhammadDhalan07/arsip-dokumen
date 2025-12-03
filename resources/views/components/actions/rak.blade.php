<button
    wire:click="
        mountAction(
            'uploadRak',
            {},
            {
                table: true,
                recordKey: '{{ $record->getKey() }}'
            }
        )
    "
    class="px-2 py-1 text-sm bg-purple-600 text-white rounded-md"
>
    RAK
</button>
