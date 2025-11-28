<?php

namespace App\Console\Commands;

use Filament\Commands\MakeUserCommand as Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    protected $description = 'Create a new user';

    protected $signature = 'make:user
                            {--name= : The name of the user}
                            {--username= : The username login of the user}
                            {--phone= : The phone of the user}
                            {--email= : A valid and unique email address}
                            {--password= : The password for the user (min. 8 characters)}
                            {--super=false : Make the user super admin}';

    /**
     * @var array{'name': string | null, 'email': string | null, 'password': string | null}
     */
    protected array $options;

    /**
     * @return array{'name': string, 'email': string, 'password': string}
     */
    protected function getUserData(): array
    {
        return [
            'name' => $this->options['name'] ?? text(
                label: 'Name',
                required: true,
            ),

            'username' => $this->options['username'] ?? text(
                label: 'Username',
                required: true,
                validate: fn (string $username): ?string => match (true) {
                    static::getUserModel()::where('username', $username)->exists() => 'A user with this username already exists',
                    default => null,
                },
            ),

            'phone' => $this->options['phone'] ?? text(
                label: 'Phone',
                validate: fn (string $phone): ?string => match (true) {
                    static::getUserModel()::where('phone', $phone)->exists() => 'A user with this phone already exists',
                    default => null,
                },
            ),

            'email' => $this->options['email'] ?? text(
                label: 'Email address',
                validate: fn (string $email): ?string => $email ? match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                }
                : null,
            ),

            'password' => Hash::make($this->options['password'] ?? password(
                label: 'Password',
                required: true,
            )),
        ];
    }

    protected function createUser(): Model&Authenticatable
    {
        $user = parent::createUser();

        $super = $this->option('super');

        if (is_null($super) || $super === 'true') {

            Artisan::call('shield:super-admin', [
                '--user' => $user->id,
            ]);
        }

        return $user;
    }
}
