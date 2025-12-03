<button
    wire:click="
        mountAction(
            'uploadLaporan',
            {},
            {
                table: true,
                recordKey: '{{ $record->getKey() }}'
            }
        )
    "
    class="px-2 py-1 text-sm bg-green-600 text-white rounded-md"
>
    Laporan
</button>
