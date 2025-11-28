@php
    $records = $this->table?->getRecords();
@endphp

@if($records instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="flex">
    <div
        class="inline-flex text-sm transition duration-75 bg-white rounded-lg shadow-sm ring-1 dark:bg-white/5 ring-gray-950/10 dark:ring-white/20">
        <div
            class="flex items-center py-1 text-gray-500 border-gray-200 dark:text-gray-400 gap-x-3 border-e pe-3 ps-3 dark:border-white/10">
            Total Records
        </div>
        <div class="flex items-center py-1 pe-3 ps-3 dark:border-white/10">
            {{ number_format($records->total()) }}
        </div>
    </div>
</div>
@endif