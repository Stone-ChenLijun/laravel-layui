<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = factory(App\Models\Menu::class);
        $this->generate($factory, config('menus'), 0);
    }

    public function generate($factory, $items, $parentId = 0)
    {
        foreach ($items as $item) {
            $permission = Permission::where('action', Arr::get($item, 'permission_action'))->first();
            $children = collect(Arr::get($item, 'children'));
            $menu = $factory->create([
                'name' => Arr::get($item, 'name'),
                'url' => $children->isEmpty() ? Arr::get($item, 'url', '') : '',
                'match' => Arr::get($item, 'match', ''),
                'permission_id' => $permission ? $permission->id : 0,
                'icon' => Arr::get($item, 'icon'),
                'parent_id' => $parentId,
            ]);
            if ($children->isNotEmpty()) {
                $this->generate($factory, $children, $menu->id);
            }
        }
    }
}
