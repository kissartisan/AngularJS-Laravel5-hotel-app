<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

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

        DB::table('users')->delete();

        $users = array(
        			['name' => 'Reymark Torres', 'email' => 'reymark.torres08@gmail.com', 'password' => Hash::make('reymark')],
        			['name' => 'Lady Morganne Lumbre', 'email' => 'ladymorgannelumbre05@gmail.com', 'password' => Hash::make('morganne')],
        			['name' => 'Admin', 'email' => 'hoteladmin@justdoit.com', 'password' => Hash::make('admin')],
        );

        foreach ($users as $user) {
        	User::create($user);
        }

        Model::reguard();
    }
}
