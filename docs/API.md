# API Reference

Complete reference for Filament resources, components, and custom classes in this Laravel 12 application.

## 📋 Table of Contents

- [Filament Resources](#filament-resources)
- [Models](#models)
- [Policies](#policies)
- [Commands](#commands)
- [Pages](#pages)
- [Components](#components)
- [Helpers](#helpers)

## Filament Resources

### UserResource

**Location**: `app/Filament/Resources/Pengaturan/Users/UserResource.php`

Main resource for user management with role-based permissions.

#### Properties

```php
protected static ?string $model = User::class;
protected static string $navigationGroup = 'Pengaturan';
protected static ?string $navigationLabel = 'Pengguna';
protected static ?string $pluralNavigationLabel = 'Pengguna';
protected static BackedEnum $navigationIcon = Heroicon::OutlinedUsers;
```

#### Methods

##### `form(Schema $schema): Schema`

Configures the user form schema using the separated `UserForm` class.

```php
public static function form(Schema $schema): Schema
{
    return UserForm::configure($schema);
}
```

##### `table(Table $table): Table`

Configures the user table using the separated `UsersTable` class.

```php
public static function table(Table $table): Table
{
    return UsersTable::configure($table);
}
```

##### `getPages(): array`

Defines the available pages for the resource.

```php
public static function getPages(): array
{
    return [
        'index' => ListUsers::route('/'),
        'create' => CreateUser::route('/create'),
        'edit' => EditUser::route('/{record}/edit'),
    ];
}
```

##### `getRecordRouteBindingEloquentQuery(): Builder`

Customizes the query for route model binding, including soft-deleted records.

```php
public static function getRecordRouteBindingEloquentQuery(): Builder
{
    return parent::getRecordRouteBindingEloquentQuery()
        ->withoutGlobalScopes([SoftDeletingScope::class]);
}
```

### UserForm Schema

**Location**: `app/Filament/Resources/Pengaturan/Users/Schemas/UserForm.php`

Handles user form configuration with proper validation and field organization.

#### `configure(Schema $schema): Schema`

```php
public static function configure(Schema $schema): Schema
{
    return $schema
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
                
            Forms\Components\TextInput::make('password')
                ->password()
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrated(fn ($state): bool => filled($state))
                ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
}
```

### UsersTable Configuration

**Location**: `app/Filament/Resources/Pengaturan/Users/Tables/UsersTable.php`

Configures the users table with columns, filters, and actions.

#### `configure(Table $table): Table`

```php
public static function configure(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('roles.name')
                ->badge()
                ->separator(','),
                
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ]),
        ]);
}
```

## Models

### User Model

**Location**: `app/Models/User.php`

Enhanced User model with ULID support, soft deletes, and role management.

#### Traits Used

- `HasUlids` - ULID primary key support
- `SoftDeletes` - Soft deletion functionality
- `HasRoles` - Spatie permission roles

#### Properties

```php
protected $fillable = [
    'name',
    'email',
    'password',
];

protected $hidden = [
    'password',
    'remember_token',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

#### Relationships

##### `roles(): BelongsToMany`

Defines the many-to-many relationship with roles.

```php
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}
```

### Role Model

**Location**: `app/Models/Role.php`

Role model for permission management using Spatie Permission package.

#### Properties

```php
use HasUlids;

protected $fillable = [
    'name',
    'guard_name',
];
```

### Permission Model

**Location**: `app/Models/Permission.php`

Permission model for granular access control.

#### Properties

```php
use HasUlids;

protected $fillable = [
    'name',
    'guard_name',
];
```

## Policies

### UserPolicy

**Location**: `app/Policies/UserPolicy.php`

Authorization policy for user-related actions, integrated with Filament Shield.

#### Methods

##### `viewAny(User $user): bool`

Determines if the user can view the users list.

```php
public function viewAny(User $user): bool
{
    return $user->can('view_any_pengaturan::users::user');
}
```

##### `view(User $user, User $model): bool`

Determines if the user can view a specific user.

```php
public function view(User $user, User $model): bool
{
    return $user->can('view_pengaturan::users::user');
}
```

##### `create(User $user): bool`

Determines if the user can create new users.

```php
public function create(User $user): bool
{
    return $user->can('create_pengaturan::users::user');
}
```

##### `update(User $user, User $model): bool`

Determines if the user can update a specific user.

```php
public function update(User $user, User $model): bool
{
    return $user->can('update_pengaturan::users::user');
}
```

##### `delete(User $user, User $model): bool`

Determines if the user can delete a specific user.

```php
public function delete(User $user, User $model): bool
{
    return $user->can('delete_pengaturan::users::user');
}
```

### RolePolicy

**Location**: `app/Policies/RolePolicy.php`

Authorization policy for role management operations.

## Commands

### MakeUserCommand

**Location**: `app/Console/Commands/MakeUserCommand.php`

Interactive command for creating users with role assignment.

#### Signature

```bash
php artisan make:user
```

#### Usage

```php
protected $signature = 'make:user';
protected $description = 'Create a new user with role assignment';

public function handle(): void
{
    $name = $this->ask('What is the user\'s name?');
    $email = $this->ask('What is the user\'s email?');
    $password = $this->secret('What is the user\'s password?');
    
    // User creation and role assignment logic
}
```

### ShieldFreshCommand

**Location**: `app/Console/Commands/ShieldFreshCommand.php`

Command for refreshing Filament Shield permissions.

#### Signature

```bash
php artisan shield:fresh
```

#### Usage

Regenerates all permissions and assigns them to the Super Admin role.

```php
protected $signature = 'shield:fresh';
protected $description = 'Refresh Filament Shield permissions';

public function handle(): void
{
    // Clear existing permissions
    // Regenerate resource permissions
    // Assign to Super Admin role
}
```

## Pages

### Dashboard

**Location**: `app/Filament/Pages/Dashboard.php`

Main dashboard page with customizable widgets and layout.

#### Properties

```php
protected static ?string $navigationIcon = 'heroicon-o-home';
protected static string $view = 'filament.pages.dashboard';
```

#### Methods

##### `getHeaderWidgets(): array`

Defines widgets to display in the dashboard header.

```php
protected function getHeaderWidgets(): array
{
    return [
        // Custom dashboard widgets
    ];
}
```

### Profile

**Location**: `app/Filament/Pages/Profile.php`

User profile management page for self-service profile editing.

#### Features

- Name and email editing
- Password change functionality
- Profile information updates

## Components

### HasUlids Trait

**Location**: `app/Concerns/HasUlids.php`

Trait for implementing ULID primary keys across models.

#### Methods

##### `usesUniqueIds(): bool`

Indicates that the model uses unique identifiers.

```php
protected function usesUniqueIds(): bool
{
    return true;
}
```

##### `newUniqueId(): string`

Generates a new ULID for the model.

```php
public function newUniqueId(): string
{
    return (string) Str::ulid();
}
```

## Helpers

### Navigation Groups

The application uses navigation groups to organize menu items:

#### Available Groups

- **Pengaturan** (Settings): User and role management
- **Dashboard**: Main dashboard and overview pages

### Icons

Using Filament's Heroicon integration for consistent iconography:

```php
use Filament\Support\Icons\Heroicon;

// Outlined icons
Heroicon::OutlinedUsers
Heroicon::OutlinedCog
Heroicon::OutlinedHome

// Solid icons
Heroicon::SolidUsers
Heroicon::SolidCog
Heroicon::SolidHome
```

### Form Components

#### Common Form Components

```php
// Text Input
Forms\Components\TextInput::make('name')
    ->required()
    ->maxLength(255)

// Email Input
Forms\Components\TextInput::make('email')
    ->email()
    ->unique(ignoreRecord: true)

// Password Input
Forms\Components\TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))

// Select with Relationship
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
```

### Table Components

#### Common Table Columns

```php
// Text Column
Tables\Columns\TextColumn::make('name')
    ->searchable()
    ->sortable()

// Badge Column
Tables\Columns\TextColumn::make('roles.name')
    ->badge()
    ->separator(',')

// DateTime Column
Tables\Columns\TextColumn::make('created_at')
    ->dateTime()
    ->sortable()
```

#### Common Table Actions

```php
// Resource Actions
Tables\Actions\EditAction::make()
Tables\Actions\DeleteAction::make()
Tables\Actions\RestoreAction::make()
Tables\Actions\ForceDeleteAction::make()

// Bulk Actions
Tables\Actions\DeleteBulkAction::make()
Tables\Actions\RestoreBulkAction::make()
Tables\Actions\ForceDeleteBulkAction::make()
```

### Filters

#### Common Filters

```php
// Trashed Filter (Soft Deletes)
Tables\Filters\TrashedFilter::make()

// Select Filter
Tables\Filters\SelectFilter::make('role')
    ->relationship('roles', 'name')

// Date Filter
Tables\Filters\Filter::make('created_from')
    ->form([
        Forms\Components\DatePicker::make('created_from'),
    ])
```

## Configuration

### Panel Configuration

**Location**: `app/Providers/Filament/AdminPanelProvider.php`

Main panel configuration including authentication, navigation, and middleware.

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors([
            'primary' => Color::Amber,
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
        ]);
}
```

---

## Related Documentation

- [Architecture Guide](ARCHITECTURE.md) - System design and patterns
- [Development Guide](DEVELOPMENT.md) - Development workflow
- [Main README](../README.md) - Project overview