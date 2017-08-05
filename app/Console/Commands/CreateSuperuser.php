<?php

namespace Castle\Console\Commands;
use Castle\Permission;
use Castle\User;

use Illuminate\Console\Command;

class CreateSuperuser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:superuser
        {--name=Administrator : Name for the user}
        {--email=root@localhost : Email for the user}
        {--password= : Set password instead of prompting for one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an account with all permissions';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = ($this->option('password')) ?:
            $this->secret('Enter a password');

        $email = $this->option('email', 'root@localhost');

        if (User::where('email', $email)->count() > 0) {
            $this->comment('Account already exists:'.PHP_EOL.'  '.$email);
        } else {
            $user = User::create([
                'name' => $this->option('name'),
                'email' => $email,
                'password' => bcrypt($password)
            ]);

            if (Permission::all()->isEmpty()) {
                $this->comment('No permissions to give. Run db:seed to seed permissions table.');
            }

            $user->permissions()->saveMany(Permission::all());

            $this->info('Superuser account created:'.PHP_EOL.'  '.$user->email);
        }
    }
}
