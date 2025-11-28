# Development Guide

Complete guide for developing with this Filament v4 Laravel 12 starter project, including workflows, conventions, and best practices.

## 📋 Table of Contents

- [Development Environment](#development-environment)
- [Project Conventions](#project-conventions)
- [Filament Development](#filament-development)
- [Laravel Development](#laravel-development)
- [Testing](#testing)
- [Code Quality](#code-quality)
- [Git Workflow](#git-workflow)
- [Troubleshooting](#troubleshooting)

## Development Environment

### Prerequisites

- **PHP 8.3+** with extensions: PDO, cURL, JSON, OpenSSL, BCMath, GD, Mbstring
- **Composer 2.0+** for dependency management
- **Node.js 18+** and **NPM** for frontend assets
- **Laravel Herd** (recommended) or Laravel Valet for local development
- **SQLite** (default) or **MySQL/PostgreSQL** for database

### Local Setup

#### 1. Initial Setup

```bash
# Clone and install dependencies
git clone <repository-url>
cd filament-v4-starter
composer install
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup (SQLite default)
touch database/database.sqlite
php artisan migrate --seed

# Create admin user
php artisan make:user
```

#### 2. Development Server

Using **Laravel Herd** (recommended):
```bash
# Project automatically available at:
# https://filament-v4-starter.test
```

Using **Composer Dev Script** (full environment):
```bash
# Starts server, queue, logs, and Vite concurrently
composer run dev
```

Manual setup:
```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Asset compilation
npm run dev

# Terminal 4: Log monitoring
php artisan pail
```

### Environment Configuration

#### Key Environment Variables

```env
# Application
APP_NAME="Filament v4 Starter"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta

# Database (SQLite default)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# For MySQL/PostgreSQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=filament_starter
DB_USERNAME=root
DB_PASSWORD=secret

# Mail (for development)
MAIL_MAILER=log

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

## Project Conventions

### File Organization

#### Laravel 12 Structure

```
app/
├── Concerns/              # Shared traits
│   └── HasUlids.php      # ULID trait for models
├── Console/Commands/      # Custom Artisan commands
├── Filament/             # Filament components
│   ├── Pages/           # Custom Filament pages
│   └── Resources/       # CRUD resources
│       └── Pengaturan/  # Settings namespace
├── Http/Controllers/     # Minimal HTTP controllers
├── Models/              # Eloquent models
├── Policies/            # Authorization policies
└── Providers/           # Service providers
```

#### Filament v4 Resource Structure

```
app/Filament/Resources/[Group]/[Entity]/
├── [Entity]Resource.php    # Main resource
├── Pages/                  # Resource pages
│   ├── Create[Entity].php
│   ├── Edit[Entity].php
│   └── List[Entity]s.php
├── Schemas/               # Form schemas
│   └── [Entity]Form.php
└── Tables/               # Table configurations
    └── [Entity]sTable.php
```

### Naming Conventions

#### Classes and Files

```php
// Resources
UserResource.php                    // Main resource
CreateUser.php, EditUser.php       // Resource pages
UserForm.php                       // Form schema
UsersTable.php                     // Table configuration

// Models
User.php                           // Singular, PascalCase
UserProfile.php                    // Compound names

// Policies
UserPolicy.php                     // Model + Policy suffix

// Commands
MakeUserCommand.php               // Descriptive + Command suffix
```

#### Database Conventions

```php
// Table names: plural, snake_case
users, user_profiles, role_user

// Column names: snake_case
first_name, created_at, user_id

// Primary keys: ULID format
id CHAR(26) PRIMARY KEY

// Foreign keys: singular_table_id
user_id, role_id
```

#### Navigation and Labels

```php
// Indonesian primary, English secondary
protected static string $navigationGroup = 'Pengaturan';    // Settings
protected static ?string $navigationLabel = 'Pengguna';     // Users
protected static ?string $pluralNavigationLabel = 'Pengguna';
```

### Code Standards

#### PHP Standards

```php
// Use PHP 8 constructor property promotion
public function __construct(
    public readonly UserService $userService,
    private readonly LoggerInterface $logger,
) {}

// Always use return type declarations
public function createUser(array $data): User
{
    return User::create($data);
}

// Use explicit null checks
if ($user !== null) {
    // Process user
}

// Prefer early returns
public function processUser(User $user): bool
{
    if (!$user->isActive()) {
        return false;
    }
    
    // Continue processing
    return true;
}
```

#### Laravel Conventions

```php
// Use Eloquent relationships over raw queries
$user->roles()->attach($roleIds);

// Use proper query scopes
User::active()->with('roles')->get();

// Use Form Requests for validation
public function store(CreateUserRequest $request): RedirectResponse
{
    $user = User::create($request->validated());
    return redirect()->route('users.index');
}

// Use resource controllers
Route::resource('users', UserController::class);
```

## Filament Development

### Creating Resources

#### 1. Generate Resource Structure

```bash
# Create main resource
php artisan make:filament-resource User --generate

# For grouped resources
php artisan make:filament-resource Pengaturan/Users/User --generate
```

#### 2. Resource Organization

```php
// app/Filament/Resources/Pengaturan/Users/UserResource.php
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string $navigationGroup = 'Pengaturan';
    
    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }
    
    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }
}
```

#### 3. Separated Form Schema

```php
// app/Filament/Resources/Pengaturan/Users/Schemas/UserForm.php
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
                
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
                
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload(),
        ]);
    }
}
```

#### 4. Separated Table Configuration

```php
// app/Filament/Resources/Pengaturan/Users/Tables/UsersTable.php
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

### Custom Pages

#### Creating Custom Pages

```bash
# Create custom page
php artisan make:filament-page Settings
```

```php
// app/Filament/Pages/Settings.php
class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action('save'),
        ];
    }
}
```

### Widgets Development

```bash
# Create widget
php artisan make:filament-widget UserStatsWidget --stats-overview
```

```php
class UserStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
```

## Laravel Development

### Model Development

#### Model Structure with ULIDs

```php
<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    use HasUlids, SoftDeletes;
    
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
    
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
```

### Migration Development

#### Creating Migrations

```bash
# Create migration
php artisan make:migration create_users_table

# Migration with ULID support
php artisan make:migration create_posts_table
```

```php
// Migration with ULID primary key
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->ulid('id')->primary();
        $table->string('title');
        $table->text('content');
        $table->ulid('user_id');
        $table->timestamps();
        $table->softDeletes();
        
        $table->foreign('user_id')->references('id')->on('users');
        $table->index(['created_at', 'deleted_at']);
    });
}
```

### Command Development

```bash
# Create command
php artisan make:command ProcessUsers
```

```php
// app/Console/Commands/ProcessUsers.php
class ProcessUsers extends Command
{
    protected $signature = 'users:process {--dry-run : Show what would be processed}';
    protected $description = 'Process user data';
    
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        $users = User::where('processed', false)->get();
        
        if ($dryRun) {
            $this->info("Would process {$users->count()} users");
            return Command::SUCCESS;
        }
        
        $bar = $this->output->createProgressBar($users->count());
        
        foreach ($users as $user) {
            // Process user
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Processing complete!');
        
        return Command::SUCCESS;
    }
}
```

### Policy Development

```bash
# Create policy
php artisan make:policy UserPolicy --model=User
```

```php
// app/Policies/UserPolicy.php
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user');
    }
    
    public function view(User $user, User $model): bool
    {
        return $user->can('view_user') || $user->id === $model->id;
    }
    
    public function create(User $user): bool
    {
        return $user->can('create_user');
    }
    
    public function update(User $user, User $model): bool
    {
        return $user->can('update_user') || $user->id === $model->id;
    }
    
    public function delete(User $user, User $model): bool
    {
        return $user->can('delete_user') && $user->id !== $model->id;
    }
}
```

## Testing

### Test Structure

```
tests/
├── Feature/           # Integration tests
│   ├── Auth/         # Authentication tests
│   ├── Filament/     # Filament panel tests
│   └── Api/          # API tests
└── Unit/             # Unit tests
    ├── Models/       # Model tests
    └── Services/     # Service tests
```

### Feature Testing

#### Filament Resource Testing

```php
// tests/Feature/Filament/UserResourceTest.php
class UserResourceTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super_admin');
        
        $this->actingAs($this->admin);
    }
    
    public function test_can_list_users(): void
    {
        $users = User::factory()->count(3)->create();
        
        livewire(ListUsers::class)
            ->assertCanSeeTableRecords($users);
    }
    
    public function test_can_create_user(): void
    {
        livewire(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
            ])
            ->call('create')
            ->assertNotified();
            
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
    
    public function test_can_edit_user(): void
    {
        $user = User::factory()->create();
        
        livewire(EditUser::class, ['record' => $user->id])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertNotified();
            
        $this->assertEquals('Updated Name', $user->fresh()->name);
    }
}
```

#### Authentication Testing

```php
// tests/Feature/Auth/AuthenticationTest.php
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        
        $response->assertStatus(200);
    }
    
    public function test_users_can_authenticate(): void
    {
        $user = User::factory()->create();
        
        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $this->assertAuthenticated();
        $response->assertRedirect('/admin');
    }
}
```

### Unit Testing

```php
// tests/Unit/Models/UserTest.php
class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_has_ulid_primary_key(): void
    {
        $user = User::factory()->create();
        
        $this->assertIsString($user->id);
        $this->assertEquals(26, strlen($user->id));
    }
    
    public function test_user_can_have_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        $user->roles()->attach($role);
        
        $this->assertTrue($user->roles->contains($role));
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Filament/UserResourceTest.php

# Run with coverage
php artisan test --coverage

# Run parallel tests
php artisan test --parallel

# Run tests with specific filter
php artisan test --filter=test_can_create_user
```

## Code Quality

### Laravel Pint (Code Formatting)

```bash
# Format all files
vendor/bin/pint

# Format specific directory
vendor/bin/pint app/Models

# Check formatting without fixing
vendor/bin/pint --test

# See what would be changed
vendor/bin/pint --diff
```

### Static Analysis

```bash
# Install PHPStan (optional)
composer require --dev phpstan/phpstan

# Run analysis
vendor/bin/phpstan analyse
```

### Pre-commit Hooks

Create `.git/hooks/pre-commit`:

```bash
#!/bin/sh

# Run Pint
vendor/bin/pint --test

if [ $? -ne 0 ]; then
    echo "Code formatting issues found. Run 'vendor/bin/pint' to fix."
    exit 1
fi

# Run tests
php artisan test

echo "All checks passed!"
```

## Git Workflow

### Branch Naming

```bash
# Feature branches
feature/user-management
feature/role-permissions

# Bug fixes
fix/user-creation-validation
fix/dashboard-loading-issue

# Hotfixes
hotfix/security-patch
hotfix/critical-bug
```

### Commit Messages

```bash
# Format: type(scope): description

# Feature commits
feat(users): add user creation with role assignment
feat(auth): implement password reset functionality

# Bug fixes
fix(users): resolve validation error on user creation
fix(dashboard): fix widget loading performance

# Documentation
docs(readme): update installation instructions
docs(api): add user resource documentation

# Refactoring
refactor(models): extract ULID trait for reusability
refactor(resources): separate form and table configurations

# Tests
test(users): add comprehensive user management tests
test(auth): add authentication flow tests
```

### Pull Request Template

```markdown
## Description
Brief description of changes.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tests pass locally
- [ ] New tests added for new functionality
- [ ] Manual testing completed

## Checklist
- [ ] Code follows project conventions
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No console errors
```

## Troubleshooting

### Common Issues

#### 1. Vite Asset Issues

```bash
# Error: Vite manifest not found
npm run build

# For development
npm run dev
```

#### 2. Database Issues

```bash
# SQLite permission issues
chmod 664 database/database.sqlite
chmod 775 database/

# Reset database
php artisan migrate:fresh --seed
```

#### 3. Permission Issues

```bash
# Storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Regenerate permissions
php artisan shield:generate --all
```

#### 4. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart queue workers
php artisan queue:restart
```

### Development Tools

#### Laravel Pail (Log Monitoring)

```bash
# Start log monitoring
php artisan pail

# Filter logs
php artisan pail --filter="level:error"
```

#### Debugging

```bash
# Enable query logging
DB::enableQueryLog();
dd(DB::getQueryLog());

# Use Laravel Debugbar (optional)
composer require --dev barryvdh/laravel-debugbar
```

### Performance Optimization

#### Database Optimization

```php
// Eager loading to prevent N+1 queries
User::with(['roles', 'permissions'])->get();

// Use chunks for large datasets
User::chunk(1000, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});

// Database indexes
Schema::table('users', function (Blueprint $table) {
    $table->index(['email', 'created_at']);
});
```

#### Caching Strategies

```php
// Cache expensive queries
$users = Cache::remember('active_users', 3600, function () {
    return User::active()->with('roles')->get();
});

// Cache configuration in production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Related Documentation

- [Architecture Guide](ARCHITECTURE.md) - System design and patterns
- [API Reference](API.md) - Detailed component reference
- [Main README](../README.md) - Project overview