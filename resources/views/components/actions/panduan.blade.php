<button
    wire:click="
        mountAction(
            'uploadPanduan',
            {},
            {
                table: true,
                recordKey: '{{ $record->getKey() }}'
            }
        )
    "
    class="px-2 py-1 text-sm bg-blue-600 text-white rounded-md"
>
    Panduan
</button>
