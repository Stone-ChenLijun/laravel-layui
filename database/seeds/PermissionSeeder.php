<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = factory(\App\Models\Permission::class);
        $this->generate($factory, config('permissions'));
    }

    public function generate($factory, $items, $parentId = 0)
    {
        foreach ($items as $key => $item) {
            $d = [
                'parent_id' => $parentId,
                'name' => Arr::get($item, 'name'),
                'action' => Arr::get($item, 'action'),
            ];
            $permission = $factory->create($d);
            $children = collect(Arr::get($item, 'children'));
            if ($children->isNotEmpty()) {
                $this->generate($factory, $children, $permission->id);
            }
        }
    }
}
