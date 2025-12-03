<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Project Name'),
                Textarea::make('description')
                    ->label('Project Description'),
                DatePicker::make('start_date')
                    ->label('Start Date'),
                DatePicker::make('end_date')
                    ->label('End Date'),
            ]);
    }
}
