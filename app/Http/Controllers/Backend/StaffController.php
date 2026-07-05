<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StaffController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:staff-list', only: ['index']),
            new Middleware('permission:staff-create', only: ['store']),
            new Middleware('permission:staff-edit', only: ['edit', 'update']),
            new Middleware('permission:staff-delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::whereNot('name', 'Super-Admin')->get();
        $staffs = Admin::all();

        return view('backend.staff.index', ['roles' => $roles, 'staffs' => $staffs]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|same:confirm-password',
            'role' => ['required', Rule::notIn('Super-Admin')],
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();
        try {
            $input = $request->all();

            $input['password'] = bcrypt($input['password']);

            $admin = Admin::create($input);
            $admin->assignRole($request->input('role'));

            DB::commit();

            $status = 'success';
            $message = __('Staff created successfully');

            notify()->$status($message, $status);

            return redirect()->route('admin.staff.index');
        } catch (\Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }

    public function edit($id)
    {
        $roles = Role::whereNot('name', 'Super-Admin')->get();
        $staff = Admin::find($id);
        $staff->getRoleNames()->first();

        return view('backend.staff.include.__edit_form', ['staff' => $staff, 'roles' => $roles])->render();
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id,
            'password' => 'same:confirm-password',
            'role' => ['required', Rule::notIn('Super-Admin')],
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();
        try {
            $input = $request->all();

            if (! empty($input['password'])) {
                $input['password'] = bcrypt($input['password']);
            } else {
                $input = Arr::except($input, ['password']);
            }

            $staff = Admin::find($id);

            if ($staff->getRoleNames()->first() === 'Super-Admin') {
                notify()->warning(__('Super admin not changeable'));

                return back();
            }

            $staff->update($input);
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $staff->assignRole($request->input('role'));

            DB::commit();

            $status = 'success';
            $message = __('Staff updated successfully');

            notify()->$status($message, $status);

            return redirect()->route('admin.staff.index');
        } catch (\Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }
}
