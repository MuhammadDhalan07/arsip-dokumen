<?php

namespace App\Filament\Resources\Pengaturan\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information')
                    ->schema([
                        TextInput::make('name')
                            ->inlineLabel()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->inlineLabel()
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('username')
                            ->inlineLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->inlineLabel()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),
                Section::make('Password & Roles')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->nullable(fn (?string $operation) => $operation === 'edit')
                            ->required(fn (?string $operation) => $operation === 'create')
                            ->dehydrateStateUsing(static function (?string $state, string $operation) {
                                if ($operation === 'create') {
                                    return ! empty($state) ? Hash::make($state) : null;
                                } elseif ($state) {
                                    return Hash::needsRehash($state) ? Hash::make($state) : $state;
                                }
                            })
                            ->columnSpanFull()
                            ->helperText('Leave empty if you dont wanna change password')
                            ->autocomplete('new-current-password')
                            ->suffixAction(
                                Action::make('generate')
                                    ->tooltip('Generate Password')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Set $set) {
                                        $set('password', Str::random(6));
                                    })
                            ),
                        Select::make('roles')
                            ->multiple()
                            ->preload()
                            ->inlineLabel()
                            ->relationship('roles', 'label')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }
}
