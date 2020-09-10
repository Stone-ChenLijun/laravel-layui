<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Role::class)->create([
            'name' => '管理员',
            'is_enable' => true,
            'sort' => 100,
        ]);
    }
}
