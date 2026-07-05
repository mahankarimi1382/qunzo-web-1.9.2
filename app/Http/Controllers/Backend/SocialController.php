<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Social;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialController extends Controller
{
    use ImageUpload;

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'icon' => 'required|mimes:png,jpg,jpeg,svg,webp',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $social = new Social;

            $data = [
                'icon' => $this->imageUploadTrait($request->icon, folderPath: 'socials'),
                'url' => $request->url,
                'position' => $social->count() + 1,
            ];

            $social->create($data);

            $status = 'success';
            $message = __('Social added successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        Social::find($id)->delete();
        notify()->success(__('Social deleted successfully'));

        return back();
    }

    public function positionUpdate(Request $request)
    {
        try {
            $inputs = $request->except('_token');
            $social = new Social;
            $i = 0;
            foreach ($inputs as $input) {
                $social->find($input)->update([
                    'position' => $i,
                ]);
                $i++;
            }

            $status = 'success';
            $message = __('Social draggable successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'nullable|mimes:png,jpg,jpeg,svg,webp',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $social = Social::find($request->id);

            $data = [
                'url' => $request->url,
            ];

            if ($request->hasFile('icon')) {
                $data['icon'] = $this->imageUploadTrait($request->icon, $social->icon, 'socials');
            }

            $social->update($data);

            $status = 'success';
            $message = __('Social updated successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }
}
