@php
    $hasPanduan = $record->getMedia('panduan')->isNotEmpty();
@endphp

@if($hasPanduan)
    {{-- Sudah upload --}}
    <div class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg ring-1 ring-inset ring-green-600/20">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <span>Panduan</span>
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
            class="ml-1 text-green-600 hover:text-green-800 transition"
            title="Lihat dokumen"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </div>
@else
    {{-- Belum upload --}}
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
        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded-lg ring-1 ring-inset ring-yellow-600/20 transition-all hover:shadow-sm"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <span>Upload Panduan</span>
    </button>
@endif
