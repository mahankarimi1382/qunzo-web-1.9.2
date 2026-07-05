<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    use ImageUpload;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'designation' => 'required',
            'picture' => 'nullable|mimes:png,jpg,jpeg,svg,webp',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            Testimonial::create([
                'name' => $request->get('name'),
                'designation' => $request->get('designation'),
                'message' => $request->get('message'),
                'picture' => $request->hasFile('picture') ? $this->imageUploadTrait($request->picture, folderPath: 'testimonials') : null,
            ]);

            $status = 'success';
            $message = __('Testimonial added successfully!');
        } catch (\Throwable $throwable) {
            $status = 'warning';
            $message = __('Sorry, something went wrong.');
        }

        notify()->$status($message, $status);

        return back();
    }

    public function edit($id)
    {
        $current_theme = site_theme();
        $testimonial = Testimonial::findOrFail($id);

        return view(sprintf('backend.page.%s.section.include.__edit_data_testimonial', $current_theme), ['testimonial' => $testimonial])->render();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'designation' => 'required',
            'picture' => 'nullable|mimes:png,jpg,jpeg,svg,webp',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $testimonial = Testimonial::findOrFail($request->id);

            $testimonial->update([
                'name' => $request->get('name'),
                'designation' => $request->get('designation'),
                'message' => $request->get('message'),
                'picture' => $request->hasFile('picture') ? $this->imageUploadTrait($request->picture, $testimonial->picture, 'testimonials') : $testimonial->picture,
            ]);

            $status = 'success';
            $message = __('Testimonial updated successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('Sorry, something went wrong.');
        }

        notify()->$status($message, $status);

        return back();
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $testimonial = Testimonial::findOrFail($request->id);

            $this->fileDelete($testimonial->picture);

            $testimonial->delete();

            DB::commit();

            $status = 'success';
            $message = __('Testimonial deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('Sorry, something went wrong.');
        }

        notify()->$status($message, $status);

        return back();
    }
}
