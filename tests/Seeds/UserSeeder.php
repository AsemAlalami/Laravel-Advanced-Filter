<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test\Seeds;


use AsemAlalami\LaravelAdvancedFilter\Test\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create(['first_name' => 'Vladimir', 'last_name' => 'Walsh', 'email' => 'congue.elit.sed@arcu.net']);
        User::create(["first_name" => "Seth", "last_name" => "Ford", "email" => "fringilla@leoinlobortis.com"]);
        User::create(["first_name" => "Nathan", "last_name" => "Benjamin", "email" => "Cras.convallis@volutpat.ca"]);
        User::create(["first_name" => "Armand", "last_name" => "Mullen", "email" => "amet@lectusquis.co.uk"]);
        User::create(["first_name" => "Jamal", "last_name" => "Whitney", "email" => "porttitor.interdum.Sed@risus.edu"]);
        User::create(["first_name" => "Judah", "last_name" => "Hughes", "email" => "consequat.auctor@elementumduiquis.org"]);
        User::create(["first_name" => "Micah", "last_name" => "Rose", "email" => "non@sed.ca"]);
        User::create(["first_name" => "Oliver", "last_name" => "Reeves", "email" => "Suspendisse.sed@ornareplacerat.edu"]);
        User::create(["first_name" => "Lucius", "last_name" => "Bradley", "email" => "sagittis@vitaealiquetnec.org"]);
        User::create(["first_name" => "Axel", "last_name" => "Hooper", "email" => "Etiam@liberoProin.co.uk"]);
    }
}
