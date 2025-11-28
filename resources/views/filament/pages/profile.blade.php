<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="fi-sc-form">
        {{ $this->form }}

        <div class="fi-ac fi-align-start">
            <x-filament::button tag="button" type="submit" form="submit">
                Save changes
            </x-filament::button>
        </div>
    </form>
    <x-filament-actions::modals />
</x-filament-panels::page>