<x-filament-panels::page>
    <div>
        <form wire:submit='submit'>
            {{ $this->form }}

            <div class="mt-6 pt-6">
                <x-filament::button type="submit">
                    Simpan
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
