<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Rules\Sort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use ResponseHelper;

    public function listView()
    {
        return view('admin/role/role_list');
    }

    public function addView()
    {
        $permissions = Permission::select(['id', 'name', 'parent_id'])->get();
        return view('admin/role/add_role', ['permissions' => $permissions]);
    }

    public function editView(Request $request)
    {
        $role = Role::find($request->id);
        if (empty($role)) {
            return abort(404, '指定角色不存在');
        }
        $permissions = Permission::select(['id', 'name', 'parent_id'])->get();
        $rolePermissionIds = $role->permissions()->pluck('id');
        return view('admin/role/edit_role', [
            'permissions' => $permissions,
            'item' => $role,
            'checkedPermissionIds' => $rolePermissionIds,
        ]);
    }

    /**
     * 角色列表
     * @\App\Annotations\Permission(action="admin.role.list")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = Role::query()->orderByDesc('sort')->orderByDesc('created_at');
        if ($request->is_enable == 1) {
            $query->where('is_enable', true);
        } elseif ($request->is_enable === 0 || $request->is_enable === '0') {
            $query->where('is_enable', false);
        }
        if (strlen($request->name)) {
            $query->where('name', 'like', "%{$request->name}%");
        }
        return $this->success_respond($query->paginate($request->page_size));
    }

    /**
     * 新建角色
     * @\App\Annotations\Permission(action="admin.role.add")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:32',
            'is_enable' => 'required|boolean',
            'sort' => ['required', new Sort()],
            'permission_id_array' => 'sometimes|array',
        ], [], [
            'name' => '角色名称',
            'is_enable' => '是否启用',
            'sort' => '排序',
            'permission_id_array' => '权限数组',
        ]);
        if (Role::where('name', $request->name)->exists()) {
            return $this->respond(-1, '已存在同名角色');
        }
        try {
            DB::beginTransaction();
            $role = new Role($request->only(['name', 'is_enable', 'sort']));
            if (!$role->save()) {
                DB::rollBack();
                return $this->respond(-1, '新建失败');
            }
            $originPermissionIds = collect($request->permission_id_array);
            if ($originPermissionIds->isNotEmpty()) {
                $permissionIds = Permission::whereIn('id', $originPermissionIds)->pluck('id');
                $role->permissions()->sync($permissionIds);
            }
            DB::commit();
            return $this->success_msg('新建成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respond(-1, '新建出错');
        }
    }

    /**
     * 编辑角色
     * @\App\Annotations\Permission(action="admin.role.edit")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|string|max:32',
            'is_enable' => 'required|boolean',
            'sort' => ['required', new Sort()],
            'permission_id_array' => 'required|array',
        ], [], [
            'id' => '角色',
            'name' => '角色名称',
            'is_enable' => '是否启用',
            'sort' => '排序',
            'permission_id_array' => '权限数组',
        ]);
        $role = Role::find($request->id);
        if (empty($role)) {
            return $this->respond(-1, '指定角色不存在');
        }
        if (Role::where('id', '!=', $role->id)->where('name', $request->name)->exists()) {
            return $this->respond(-1, '已存在同名角色');
        }
        try {
            DB::beginTransaction();
            $role = $role->fill($request->only(['name', 'is_enable', 'sort']));
            $permissionIds = Permission::whereIn('id', collect($request->permission_id_array))->pluck('id');
            $role->permissions()->sync($permissionIds);
            if (!$role->save()) {
                DB::rollBack();
                return $this->respond(-1, '编辑失败');
            }
            DB::commit();
            return $this->success_msg('编辑成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respond(-1, '编辑出错');
        }
    }

    /**
     * 删除角色
     * @\App\Annotations\Permission(action="admin.role.delete")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ], [], [
            'id' => '角色',
        ]);
        $role = Role::find($request->id);
        if (empty($role)) {
            return $this->respond(-1, '指定角色不存在');
        }
        $count = $role->users()->count();
        if ($count) {
            return $this->respond(-1, "指定角色下有{$count}个用户，无法删除");
        }
        return $role->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除失败');
    }
}
