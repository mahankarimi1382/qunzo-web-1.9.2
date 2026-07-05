<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:role-list', ['only' => ['index']]),
            new Middleware('permission:role-create', ['only' => ['create', 'store']]),
            new Middleware('permission:role-edit', ['only' => ['edit', 'update']]),
            new Middleware('permission:role-delete', ['only' => ['destroy']]),
        ];
    }

    public function index()
    {
        $roles = Role::all();

        return view('backend.roles.index', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();

        try {
            $role = Role::create(['name' => $request->input('name')]);
            $permissionNames = Permission::whereIn('id', $request->input('permission'))->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);

            DB::commit();

            notify()->success(__('Role created successfully'));

            return redirect()->route('admin.roles.index');
        } catch (\Exception $exception) {
            DB::rollBack();

            notify()->warning(__('something is wrong: ').$exception->getMessage());

            return back();
        }
    }

    public function create()
    {
        $permissions = Permission::get()->groupBy('category');

        return view('backend.roles.create', ['permissions' => $permissions]);
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get()->groupBy('category');
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('backend.roles.edit', ['role' => $role, 'permissions' => $permissions, 'rolePermissions' => $rolePermissions]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();

        try {
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();

            $permissionNames = Permission::whereIn('id', $request->input('permission'))->pluck('name')->toArray();

            $role->syncPermissions($permissionNames);

            DB::commit();

            $status = 'success';
            $message = __('Role updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }
}
