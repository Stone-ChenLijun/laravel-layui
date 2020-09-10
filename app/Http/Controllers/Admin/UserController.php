<?php

namespace App\Http\Controllers\Admin;

use App\Annotations\Permission;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Account;
use App\Rules\Password;
use App\Services\AppConfigService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthenticatesUsers, ResponseHelper;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function loginView()
    {
        $config = app('app.config')->scope(AppConfigService::SCOPE_APP);
        return view('admin/user/login', [
            'icp' => $config->get('icp')->value,
            'policeIcp' => $config->get('police_icp')->value,
        ]);
    }

    public function username()
    {
        return 'account';
    }

    protected function authenticated(Request $request, $user)
    {
        return $this->success_respond(['redirect_url' => $this->redirectPath()], '登录成功');
    }

    public function listView()
    {
        return view('admin/user/user_list');
    }

    public function addView()
    {
        $roles = Role::all(['id', 'name']);
        return view('admin/user/add_user', ['roles' => $roles]);
    }

    public function editView(Request $request)
    {
        $user = User::find($request->id);
        if (empty($user)) {
            return abort(404, '指定用户不存在');
        }
        $roles = Role::all(['id', 'name']);
        $checkedRoleIds = $user->roles()->pluck('id');
        return view('admin/user/edit_user', ['item' => $user, 'roles' => $roles, 'checkRoleIds' => $checkedRoleIds]);
    }

    /**
     * 用户列表
     * @Permission(action="admin.user.list")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = User::query()
            ->with([
                'roles' => function ($query) {
                    $query->select(['id', 'name']);
                }
            ])
            ->orderByDesc('is_super_admin')
            ->orderByDesc('created_at');
        if (strlen($request->name)) {
            $query->where('name', 'like', "%{$request->name}%");
        }
        if (strlen($request->account)) {
            $query->where('account', 'like', "%{$request->account}%");
        }
        if (strlen($request->email)) {
            $query->where('email', 'like', "%{$request->email}%");
        }
        $result = $query->paginate($request->page_size);
        $result->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'account' => $item->account,
                'is_super_admin' => $item->is_super_admin,
                'email' => $item->email,
                'email_verified_at' => $item->email_verified_at,
                'roles' => $item->roles->transform(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                }),
                'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
            ];
        });
        return $this->success_respond($result);
    }

    /**
     * 新建用户
     * @Permission(action="admin.user.add")
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:32',
            'account' => ['required', new Account()],
            'password' => ['required', new Password()],
            'email' => 'present|email|max:255',
            'role_id_array' => 'sometimes|array|min:1',
        ], [], [
            'name' => '用户名',
            'account' => '帐号',
            'password' => '密码',
            'email' => '邮箱',
            'role_id_array' => '角色',
        ]);
        if (User::where('account', $request->account)->exists()) {
            return $this->ret(-1, '同名帐号已存在');
        }
        if (strlen($request->email) && User::where('email', $request->email)->exists()) {
            return $this->ret(-1, '相同邮箱已存在');
        }
        try {
            DB::beginTransaction();
            $user = new User($request->only(['account', 'name', 'email']));
            $user->password = Hash::make($request->password);
            if (!$user->save()) {
                DB::rollBack();
                return $this->respond(-1, '新建失败');
            }
            $originRoleIds = collect($request->role_id_array);
            if ($originRoleIds->isNotEmpty()) {
                $existRoleIds = Role::whereIn('id', $request->role_id_array)->pluck('id');
                $user->roles()->sync($existRoleIds);
            }
            DB::commit();
            return $this->success_msg('新建成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respond(-1, '新建出错');
        }
    }

    /**
     * 修改用户
     * @Permission(action="admin.user.edit")
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required|string|max:32',
            'account' => ['required', new Account()],
            'email' => 'present|email|max:255',
            'role_id_array' => 'sometimes|array|min:1',
        ], [], [
            'id' => 'required|integer',
            'name' => '用户名',
            'account' => '帐号',
            'email' => '邮箱',
            'role_id_array' => '角色',
        ]);
        $user = User::find($request->id);
        if (empty($user)) {
            return $this->respond(-1, '指定用户不存在');
        }
        if ($user->is_super_admin) {
            $this->respond(-1, '不允许操作超级管理员');
        }
        if (User::where('account', $request->account)->where('id', '!=', $user->id)->exists()) {
            return $this->ret(-1, '同名帐号已存在');
        }
        if (strlen($request->email) && User::where('email', $request->email)->where('id', '!=', $user->id)->exists()) {
            return $this->ret(-1, '相同邮箱已存在');
        }
        $user->fill($request->only(['name', 'account', 'email']));
        try {
            DB::beginTransaction();
            if (!$user->save()) {
                DB::rollBack();
                return $this->respond(-1, '修改失败');
            }
            $originRoleIds = collect($request->role_id_array);
            if ($originRoleIds->isNotEmpty()) {
                $existRoleIds = Role::whereIn('id', $request->role_id_array)->pluck('id');
                $user->roles()->sync($existRoleIds);
            }
            DB::commit();
            return $this->success_msg('修改成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respond(-1, '修改出错');
        }
    }

    /**
     * 删除用户
     * @Permission(action="admin.user.delete")
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ], [], [
            'id' => '用户',
        ]);
        $user = User::find($request->id);
        if (empty($user)) {
            return $this->respond(-1, '指定用户不存在');
        }
        if ($user->is_super_admin) {
            $this->respond(-1, '不允许操作超级管理员');
        }
        return $user->delete() ? $this->success_msg('删除成功') : $this->respond(-1, '删除失败');
    }

    /**
     * 修改密码
     * @Permission()
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'new_password' => ['required', new Password()],
        ], [], [
            'id' => '用户',
            'new_password' => '新密码',
        ]);
        $user = User::find($request->id);
        if (empty($user)) {
            return $this->respond(-1, '指定用户不存在');
        }
        if ($user->is_super_admin) {
            $this->respond(-1, '不允许操作超级管理员');
        }
        $user->password = Hash::make($request->new_password);
        return $user->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 修改密码
     * @Permission()
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string|max:12',
            'new_password' => ['required', new Password()],
        ], [], [
            'old_password' => '旧密码',
            'new_password' => '新密码',
        ]);
        $user = auth()->user();
        if (!Hash::check($request->old_password, $user->getAuthPassword())) {
            return $this->respond(-1, '旧密码错误');
        }
        $user->password = Hash::make($request->new_password);
        return $user->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }

    /**
     * 修改用户个人信息
     * @Permission()
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editSelf(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:32',
            'account' => ['required', new Account()],
            'email' => 'present|email|max:255',
        ], [], [
            'name' => '用户名',
            'account' => '帐号',
            'email' => '邮箱',
        ]);
        $user = auth()->user();
        if (User::where('account', $request->account)->where('id', '!=', $user->id)->exists()) {
            return $this->ret(-1, '同名帐号已存在');
        }
        if (strlen($request->email) && User::where('email', $request->email)->where('id', '!=', $user->id)->exists()) {
            return $this->ret(-1, '相同邮箱已存在');
        }
        $user->fill($request->only(['name', 'account', 'email']));
        return $user->save() ? $this->success_msg('修改成功') : $this->respond(-1, '修改失败');
    }
}
