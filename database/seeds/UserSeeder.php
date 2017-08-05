<?php

use Illuminate\Database\Seeder;
use Castle\Permission;
use Castle\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 5)->create()->each(function($u) {
            $u->permissions()->saveMany(
                Permission::byType(Permission::DEFAULT_TYPE)->get()->all()
            );
        });
    }
}
