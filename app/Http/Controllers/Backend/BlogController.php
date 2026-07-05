<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Language;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    use ImageUpload;

    public function create()
    {
        return view('backend.page.blog.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cover' => 'required|image|mimes:jpg,png,svg,webp',
            'title' => 'required',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $maxId = Blog::max('id');

            if (! $maxId) {
                Blog::query()->truncate();
                $maxId = 1;
            } else {
                $maxId += 1;
            }

            $data = [
                'cover' => self::imageUploadTrait($request->cover, folderPath: 'blogs'),
                'title' => $request->title,
                'details' => $request->details,
                'locale_id' => $maxId,
            ];

            Blog::create($data);

            $status = 'success';
            $message = __('Blog added successfully!');

            notify()->$status($message, $status);

            return redirect()->route('admin.page.edit', 'blog');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }

    public function edit($id)
    {
        $blog = Blog::where('locale_id', $id)->get();

        $engBlog = Blog::where('locale_id', $id)->where('locale', '=', 'en')->firstOrFail(['id', 'cover', 'title', 'details'])->toArray();

        $groupData = $blog->groupBy('locale');
        $groupData = $groupData->map(function ($items) {
            return $items->first()->only(['id', 'cover', 'title', 'details']);
        })->toArray();

        $languages = Language::where('status', true)->get();

        $locale = array_column($languages->toArray(), 'locale');
        $localeKey = array_fill_keys($locale, $engBlog);
        $groupData = array_merge($localeKey, $groupData);

        return view(sprintf('backend.page.blog.edit'), ['groupData' => $groupData, 'languages' => $languages]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $locale = $request->locale;
            $blog = Blog::where('locale', $locale)->where('id', $id)->first();
            $engBlog = Blog::where('id', $id)->where('locale', '=', 'en')->first();

            if (! $blog) {
                $blog = $engBlog->replicate();
                $blog->locale = $locale;
                $blog->created_at = $engBlog->un_modify_created_at;
                $blog->save();
            }

            $data = [
                'title' => $request->title,
                'details' => $request->details,
            ];

            if ($request->hasFile('cover')) {
                $data['cover'] = self::imageUploadTrait($request->cover, $blog->cover, folderPath: 'blogs');
            }

            $blog->update($data);

            $status = 'success';
            $message = __('Blog updated successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::where('id', $id);
            if (file_exists(public_path($blog->first()?->cover))) {
                @unlink(public_path($blog->first()->cover));
            }

            Blog::where('id', $id)->delete();

            $status = 'success';
            $message = __('Blog deleted successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return redirect()->route('admin.page.edit', 'blog');
    }
}
