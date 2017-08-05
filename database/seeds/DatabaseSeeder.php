<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $seeds = [
            PermissionSeeder::class,
            ResourceTypeSeeder::class,
            DiscussionStatusSeeder::class,
        ];

        $env = App::environment();

        switch ($env) {

            case 'local':
            case 'development':
            case 'dev':

                echo 'In development mode: going to seed more things.'.PHP_EOL;

                $seeds = array_merge($seeds, [
                    UserSeeder::class,
                    TagSeeder::class,
                    DocumentSeeder::class,
                    ClientSeeder::class,
                    ResourceSeeder::class,
                    DiscussionSeeder::class,
                    CommentSeeder::class,
                    VoteSeeder::class,
                ]);

        }

        foreach ($seeds as $class) {
           	$this->call($class);
        }

        Model::reguard();
    }
}
