<?php


namespace App\Services;


use App\Models\Menu;
use App\Models\Permission;

class MenuService
{
    // 当前请求的uri
    protected $requestUri;
    // 当前uri匹配的菜单项id数组
    protected $matchMenuIds;

    public function __construct()
    {
        $this->requestUri = request()->getRequestUri();
        $this->getMatchMenuIds();
    }

    public function getMatchMenuIds()
    {
        if (!empty($this->matchMenuIds)) {
            return $this->matchMenuIds;
        }
        $loop = Menu::query()
            ->doesntHave('children')
            ->whereRaw("'{$this->requestUri}' REGEXP `match`")
            ->first();
        $matchMenuTreeIds = collect();
        do {
            $matchMenuTreeIds->add($loop->id);
        } while ($loop = $loop->parent);
        $this->matchMenuIds = $matchMenuTreeIds;
        return $this->matchMenuIds;
    }

    public function getMenuTree()
    {
        $user = auth()->user();
        $query = Menu::query()->where('parent_id', 0);
        if (!$user) {
            return $query->where('permission_id', 0)
                ->with([
                    'children' => function ($query) {
                        $query->where('permission_id', 0);
                    }
                ])->get();
        }
        if ($user->is_super_admin) {
            return $query->with('children')->get();
        }
        $permissionIds = Permission::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('id', $user->roles()->where('is_enable', true)->pluck('id'));
        })->pluck('id')->add(0);
        return $query->whereIn('permission_id', $permissionIds)
            ->with([
                'children' => function ($query) use ($permissionIds) {
                    $query->whereIn('permission_id', $permissionIds);
                }
            ])
            ->get();
    }

    public function isInMenuTree(Menu $menu)
    {
        return $this->getMatchMenuIds()->contains($menu->id);
    }
}
