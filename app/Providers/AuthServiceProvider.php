<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (Schema::hasTable('permissions')) {
            Permission::all()->each(function ($permission, $key) {
                Gate::define($permission->action, function ($user) use($permission) {
                    if ($user->is_super_admin) {
                        return true;
                    }
                    $roleIdArray = $user->roles()->where('is_enable', true)->pluck('id');
                    return DB::table('role_permission_pivot')->whereIn('role_id', $roleIdArray)
                        ->where('permission_id', $permission->id)->exists();
                });
            });
        }
    }
}
