<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i<100; $i++) {
            $user = new \App\User();
            $user->name= 'Name'.$i;
            $user->surname= 'SurName'.$i;
            $user->email= 'e'.$i.'@ee.ee';
            $user->status= 'status'.$i;
            $user->password = Hash::make('password'.$i);
            $user->save();
        }
    }
}
