<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class Profile extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected string $view = 'filament.pages.profile';

    public User $user;

    public ?array $data = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->fillForm();
    }

    protected function fillForm()
    {
        $data = [
            'name' => $this->user?->name,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'username' => $this->user?->username,
            'current_password' => null,
            'password' => null,
            'password_confirmation' => null,
        ];

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->inlineLabel()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->inlineLabel()
                            ->required()
                            ->email()
                            ->rules([
                                Rule::unique('users', 'email')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                        TextInput::make('username')
                            ->inlineLabel()
                            ->required()
                            ->rules([
                                Rule::unique('users', 'username')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->inlineLabel()
                            ->rules([
                                Rule::unique('users', 'phone')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Old Password')
                            ->requiredWith('password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->columnSpanFull()
                            ->helperText('Leave empty if you dont wanna change password')
                            ->autocomplete('new-current-password'),
                        TextInput::make('password')
                            ->label('New Password')
                            ->requiredWith('current_password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->columnSpanFull()
                            ->autocomplete('new-password'),
                        TextInput::make('password_confirmation')
                            ->label('New Password (Confirm)')
                            ->inlineLabel()
                            ->password()
                            ->columnSpanFull()
                            ->revealable()
                            ->autocomplete('new-password-confirmation'),
                    ]),
            ])
            ->statePath('data')
            ->model($this->user);
    }

    public function submit()
    {
        $input = $this->form->getState();

        $update = [
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'phone' => $input['phone'],
        ];

        if (filled($input['password'])) {
            if (Hash::check($input['current_password'], $this->user->password)) {
                $update['password'] = Hash::make($input['password']);
            } else {
                throw ValidationException::withMessages([
                    'data.current_password' => 'Your current password not match.',
                ]);
            }
        }

        $this->user->update($update);

        Notification::make()
            ->title('Profil berhasil diupdate')
            ->success()
            ->send();

        $this->fillForm();
    }
}
