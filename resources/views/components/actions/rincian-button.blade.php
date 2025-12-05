@php
    $collection = Str::snake($rincian->name);
    $hasFile = $record->getMedia($collection)->isNotEmpty();
    $slug = Str::slug($rincian->name, '_');
@endphp

@if($hasFile)
    <div class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg ring-1 ring-inset ring-green-600/20">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>

        <span>{{ strtoupper($rincian->name) }}</span>

        <button
            wire:click="mountAction(
                'upload_{{ $slug }}',
                {},
                { table: true, recordKey: '{{ $record->getKey() }}' }
            )"
            class="ml-1 text-green-600 hover:text-green-800 transition"
            title="Lihat dokumen"
        >
            <x-heroicon-o-eye class="w-4 h-4"/>
        </button>
    </div>
@else
    <button
        wire:click="mountAction(
            'upload_{{ $slug }}',
            {},
            { table: true, recordKey: '{{ $record->getKey() }}' }
        )"
        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg ring-1 ring-inset ring-purple-600/20 transition-all hover:shadow-sm"
    >
        <x-heroicon-o-arrow-up-tray class="w-4 h-4"/>
        <span>Upload {{ strtoupper($rincian->name) }}</span>
    </button>
@endif
