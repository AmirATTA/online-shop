<?php

namespace Modules\Permission\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Modules\Permission\Http\Requests\Admin\RoleStoreRequest;
use Modules\Permission\Http\Requests\Admin\RoleUpdateRequest;

class RoleController extends Controller
{
    private function permissions(): Collection
    {
//        return Cache::rememberForever('all_permissions', function () {
//            return Permission::query()
//                ->oldest('id')
//                ->select(['id', 'name', 'label'])
//                ->get();
//        });

        return Permission::query()
            ->oldest('id')
            ->select(['id', 'name', 'label'])
            ->get();
    }

    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->latest('id')
            ->select(['id', 'name', 'label', 'created_at'])
            ->paginate();

        return response()->success('',compact('roles'));
    }
    
    public function store(RoleStoreRequest $request): JsonResponse
    {
        $role = Role::query()->create([
            'name' => $request->input('name'),
            'label' => $request->input('label'),
            'guard_name' => 'admin-api'
        ]);
        
        $permissions = $request->input('permissions');
        if ($permissions) {
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }
        
        return response()->success('نقش با موفقیت ثبت شد.');
    }
    
    
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $role->update($request->only(['name', 'label']));
        
        $permissions = $request->input('permissions');
        $role->syncPermissions($permissions);
        
        return response()->success('نقش با موفقیت به روزرسانی شد.');
    }
    
    public function destroy(Role $role): JsonResponse
    {
        $permissions = $role->permissions;
        if ($role->delete()) {
            foreach ($permissions as $permission) {
                $role->revokePermissionTo($permission);
            }
        }
        return response()->success('نقش با موفقیت حذف شد.');
    }
}
