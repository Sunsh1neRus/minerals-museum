<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
                ['id' => 1, 'name' => 'admin'],
                ['id' => 2, 'name' => 'moderator'],
                ['id' => 3, 'name' => 'editor'],
                ['id' => 4, 'name' => 'newsman'],
                ['id' => 5, 'name' => 'simple']]
        );
    }
}
