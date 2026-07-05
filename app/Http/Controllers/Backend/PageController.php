<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use App\Models\LandingPage;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageSetting;
use App\Models\Social;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PageController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:footer-manage|landing-page-manage', ['only' => ['landingSectionUpdate']]),
            new Middleware('permission:landing-page-manage', ['only' => ['landingSection', 'contentStore', 'contentUpdate', 'contentDelete']]),
            new Middleware('permission:footer-manage', ['only' => ['footerContent']]),
            new Middleware('permission:page-setting', ['only' => ['pageSetting', 'pageSettingUpdate', 'settingUpdate']]),
        ];
    }

    // ================================== page section ===============================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'nullable',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $slug = Str::slug($request->title, '-');

            $page = new Page;

            if ($page->where('code', $slug)->exists()) {
                notify()->error(__('Page already exists'));

                return back();
            }

            $pageType = $request->page_type ?? 'dynamic';

            if ($pageType == 'service') {
                $content = [
                    'meta_keywords' => $request->meta_keywords,
                    'meta_description' => $request->meta_description,
                    'hero_title' => '',
                    'hero_description' => '',
                    'hero_button_text' => '',
                    'hero_button_link' => '',
                    'hero_button_target' => '_self',
                    'hero_image' => null,
                    'highlight_title' => '',
                    'highlight_description' => '',
                    'highlight_button_text' => '',
                    'highlight_button_link' => '',
                    'highlight_button_target' => '_self',
                    'highlight_image' => null,
                    'features_title' => '',
                    'steps_title' => '',
                    'faq_title' => '',
                    'faq_subtitle' => '',
                    'faq_image' => null,
                ];
            } else {
                $content = [
                    'meta_keywords' => $request->meta_keywords,
                    'meta_description' => $request->meta_description,
                    'section_id' => json_encode($request->get('section_id', [])),
                    'content' => Purifier::clean(htmlspecialchars_decode($request->content)),
                ];
            }

            $pageData = [
                'title' => $request->title,
                'url' => $slug,
                'code' => $slug,
                'type' => $pageType,
                'data' => json_encode($content),
                'status' => $request->status,
                'theme' => site_theme(),
            ];

            if ($pageType == 'service') {
                $pageData['type'] = 'service';
            }

            $page->create($pageData);

            Cache::pull('pages');

            notify()->success(__('New Page Created Successfully'));

            return redirect()->route('admin.page.edit', $slug);
        } catch (\Exception $exception) {
            notify()->warning(__('something is wrong: ').$exception->getMessage());

            return back();
        }
    }

    public function create()
    {
        $currentTheme = site_theme();
        $landingSections = LandingPage::currentTheme()->where('locale', app()->getLocale())->whereNot('code', 'footer')->orderBy('sort')->get();

        return view(sprintf('backend.page.%s.create', $currentTheme), ['landingSections' => $landingSections]);
    }

    public function edit($name)
    {
        $currentTheme = site_theme();
        $page = Page::currentTheme()->where('code', $name)->get();
        $engPage = Page::currentTheme()->where('code', $name)->where('locale', '=', 'en')->first();

        $status = $engPage->status;
        $slug = $engPage->url;
        $groupData = $page->groupBy('locale');

        $groupData = $groupData->map(function ($items) {

            $item = $items->first();
            if ($item->type == 'dynamic' || $item->type == 'service') {
                return array_merge(json_decode($items->first()->data, true), ['title' => $item->title]);
            }

            return json_decode($item->data, true);
        })->toArray();

        $languages = Language::where('status', true)->get();
        $locale = array_column($languages->toArray(), 'locale');
        $engData = json_decode($engPage->data, true);
        $localeKey = array_fill_keys($locale, $engData);
        $groupData = array_merge($localeKey, $groupData);

        $commaIds = isset($engData['section_id']) ? implode(',', json_decode($engData['section_id'])) : '';
        $landingSections = LandingPage::currentTheme()->where('status', true)->where('code', '!=', 'footer')->where('locale', 'en')->when(isset($engData['section_id']) && json_decode($engData['section_id']), function ($query) use ($commaIds) {
            $query->orderByRaw(sprintf('FIELD(id, %s)', $commaIds));
        })->get();

        if ($engPage->type == 'dynamic') {
            $title = $engPage->title;
            $code = $engPage->code;

            return view(sprintf('backend.page.%s.edit', $currentTheme), ['landingSections' => $landingSections, 'title' => $title, 'groupData' => $groupData, 'status' => $status, 'code' => $code, 'languages' => $languages]);
        }

        if ($engPage->type == 'service') {
            $title = $engPage->title;
            $code = $engPage->code;

            // Load service page contents
            $features = LandingContent::currentTheme()
                ->where('type', 'service-'.$code.'-features')
                ->where('locale', 'en')->get();
            $steps = LandingContent::currentTheme()
                ->where('type', 'service-'.$code.'-steps')
                ->where('locale', 'en')->get();
            $faqs = LandingContent::currentTheme()
                ->where('type', 'service-'.$code.'-faqs')
                ->where('locale', 'en')->get();

            return view(sprintf('backend.page.%s.service-edit', $currentTheme), [
                'title' => $title,
                'groupData' => $groupData,
                'status' => $status,
                'code' => $code,
                'languages' => $languages,
                'features' => $features,
                'steps' => $steps,
                'faqs' => $faqs,
            ]);
        }

        return view(sprintf('backend.page.%s.%s', $currentTheme, $name), ['status' => $status, 'landingSections' => $landingSections, 'slug' => $slug, 'groupData' => $groupData, 'languages' => $languages]);
    }

    public function update(Request $request)
    {
        try {
            $content = $request->except(['page_code', 'status', '_token', 'page_locale']);
            $pageCode = $request->page_code;
            $pageLocale = $request->page_locale;

            $engPage = Page::currentTheme()->where('code', $pageCode)->where('locale', '=', 'en')->first();
            $page = Page::currentTheme()->where('code', $pageCode)->where('locale', $pageLocale)->first();

            if (! $page) {
                $page = $engPage->replicate();
                $page->locale = $pageLocale;
                $page->save();
            }

            $page = Page::currentTheme()->where('code', $pageCode)->where('locale', $pageLocale)->first();

            $uploadedImages = [];

            if ($page->type == 'dynamic') {
                $content = [
                    'section_id' => json_encode($request->get('section_id', [])),
                    'meta_keywords' => $request->meta_keywords,
                    'meta_description' => $request->meta_description,
                    'content' => Purifier::clean(htmlspecialchars_decode($request->content)),
                ];

                if ($pageLocale != 'en') {
                    $engOldData = json_decode($engPage->data, true);
                    $content = array_merge($engOldData, $content);
                } else {
                    $content['section_id'] = json_encode($request->get('section_id', []));
                }

                if ($request->section_id == null) {
                    $content['section_id'] = json_encode([]);
                }

                $data = [
                    'title' => $request->title,
                    'data' => json_encode($content),
                    'status' => (bool) $request->status,
                ];
            } elseif ($page->type == 'service') {
                $oldData = json_decode($page->data, true) ?? [];
                $engOldData = json_decode($engPage->data, true) ?? [];

                $content = [
                    'meta_keywords' => $request->meta_keywords ?? $engOldData['meta_keywords'] ?? '',
                    'meta_description' => $request->meta_description ?? $engOldData['meta_description'] ?? '',
                    'hero_title' => $request->hero_title ?? $engOldData['hero_title'] ?? '',
                    'hero_description' => $request->hero_description ?? $engOldData['hero_description'] ?? '',
                    'hero_button_text' => $request->hero_button_text ?? $engOldData['hero_button_text'] ?? '',
                    'hero_button_link' => $request->hero_button_link ?? $engOldData['hero_button_link'] ?? '',
                    'hero_button_target' => $request->hero_button_target ?? $engOldData['hero_button_target'] ?? '_self',
                    'highlight_title' => $request->highlight_title ?? $engOldData['highlight_title'] ?? '',
                    'highlight_description' => $request->highlight_description ?? $engOldData['highlight_description'] ?? '',
                    'highlight_button_text' => $request->highlight_button_text ?? $engOldData['highlight_button_text'] ?? '',
                    'highlight_button_link' => $request->highlight_button_link ?? $engOldData['highlight_button_link'] ?? '',
                    'highlight_button_target' => $request->highlight_button_target ?? $engOldData['highlight_button_target'] ?? '_self',
                    'features_title' => $request->features_title ?? $engOldData['features_title'] ?? '',
                    'steps_title' => $request->steps_title ?? $engOldData['steps_title'] ?? '',
                    'faq_title' => $request->faq_title ?? $engOldData['faq_title'] ?? '',
                    'faq_subtitle' => $request->faq_subtitle ?? $engOldData['faq_subtitle'] ?? '',
                ];

                // Handle image uploads
                if ($request->hasFile('faq_image')) {
                    $oldValue = Arr::get($oldData, 'faq_image');
                    $content['faq_image'] = self::imageUploadTrait($request->faq_image, $oldValue, folderPath: 'images');
                    $uploadedImages['faq_image'] = $content['faq_image'];
                } else {
                    $content['faq_image'] = Arr::get($oldData, 'faq_image', Arr::get($engOldData, 'faq_image'));
                }

                if ($request->hasFile('hero_image')) {
                    $oldValue = Arr::get($oldData, 'hero_image');
                    $content['hero_image'] = self::imageUploadTrait($request->hero_image, $oldValue, folderPath: 'images');
                    $uploadedImages['hero_image'] = $content['hero_image'];
                } else {
                    $content['hero_image'] = Arr::get($oldData, 'hero_image', Arr::get($engOldData, 'hero_image'));
                }

                if ($request->hasFile('highlight_image')) {
                    $oldValue = Arr::get($oldData, 'highlight_image');
                    $content['highlight_image'] = self::imageUploadTrait($request->highlight_image, $oldValue, folderPath: 'images');
                    $uploadedImages['highlight_image'] = $content['highlight_image'];
                } else {
                    $content['highlight_image'] = Arr::get($oldData, 'highlight_image', Arr::get($engOldData, 'highlight_image'));
                }

                if ($pageLocale != 'en') {
                    $content = array_merge($engOldData, $content);
                }

                $data = [
                    'title' => $request->title,
                    'data' => json_encode($content),
                    'status' => (bool) $request->status,
                ];

                // If editing in English and images were uploaded, update all other languages with the same images
                if ($pageLocale === 'en' && ! empty($uploadedImages)) {
                    $locales = Language::where('status', true)
                        ->where('locale', '!=', 'en')
                        ->pluck('locale');

                    $pages = Page::currentTheme()
                        ->where('code', $pageCode)
                        ->whereIn('locale', $locales)
                        ->get()
                        ->keyBy('locale');

                    foreach ($pages as $otherPage) {
                        $otherData = json_decode($otherPage->data, true) ?? [];
                        $otherData = array_replace($otherData, $uploadedImages);
                        $otherPage->update(['data' => json_encode($otherData)]);
                    }
                }

                $page->update($data);
                Cache::pull('pages');

                $status = 'success';
                $message = $page->title.' '.__('updated successfully');
                notify()->$status($message, $status);

                return back();
            } else {

                $oldData = json_decode($page->data, true);
                $engOldData = json_decode($engPage->data, true);

                foreach ($content as $key => $value) {
                    if (is_array($value)) {
                        $content[$key] = json_encode($value);
                    } elseif (is_file($value)) {
                        $oldValue = Arr::get($oldData, $key);
                        $content[$key] = self::imageUploadTrait($value, $oldValue);
                        $uploadedImages[$key] = $content[$key];
                    } elseif ($key == 'content') {
                        $content[$key] = Purifier::clean(htmlspecialchars_decode($value));
                    }
                }

                $content = array_merge($engOldData, $content);
                $content = array_merge($oldData, $content);

                if ($request->section_id == null) {
                    $content['section_id'] = json_encode([]);
                }

                $data = [
                    'data' => json_encode($content),
                ];

                if ($pageLocale == 'en' && (property_exists($request, 'status') && $request->status !== null)) {
                    Page::currentTheme()->where('code', $pageCode)->update([
                        'status' => (bool) $request->status,
                    ]);
                }
            }

            $page->update($data);

            // If editing in English and images were uploaded, update all other languages with the same images
            if ($pageLocale === 'en' && ! empty($uploadedImages) && $page->type != 'dynamic') {
                $locales = Language::where('status', true)
                    ->where('locale', '!=', 'en')
                    ->pluck('locale');

                $pages = Page::currentTheme()
                    ->where('code', $pageCode)
                    ->whereIn('locale', $locales)
                    ->get()
                    ->keyBy('locale');

                foreach ($pages as $otherPage) {
                    $otherData = json_decode($otherPage->data, true) ?? [];

                    // Merge images
                    $otherData = array_replace($otherData, $uploadedImages);

                    $otherPage->update([
                        'data' => json_encode($otherData),
                    ]);
                }
            }

            if ($page->type == 'dynamic') {
                Cache::pull('pages');
            }

            $status = 'success';
            $message = $page->title.' '.__('updated successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function deleteNow(Request $request)
    {
        try {
            $pageCode = $request['page_code'];
            $pages = Page::currentTheme()->where('code', $pageCode)->get();

            if ($pages->isEmpty()) {
                notify()->warning(__('Page not found'));

                return back();
            }

            $theme = site_theme();
            $imageFields = ['hero_image', 'highlight_image'];

            // Delete images from page data and service-type related contents
            foreach ($pages as $page) {
                $data = json_decode($page->data, true);
                if (is_array($data)) {
                    foreach ($imageFields as $field) {
                        if (! empty($data[$field])) {
                            $path = $data[$field];
                            if (is_string($path) && file_exists(public_path($path)) && is_file(public_path($path))) {
                                $this->fileDelete($path);
                            }
                        }
                    }
                }
            }

            // For service pages, delete all related landing contents and their icon files
            $firstPage = $pages->first();
            if ($firstPage->type === 'service') {
                $serviceTypes = [
                    'service-'.$pageCode.'-features',
                    'service-'.$pageCode.'-steps',
                    'service-'.$pageCode.'-faqs',
                ];

                $contents = LandingContent::where('theme', $theme)
                    ->whereIn('type', $serviceTypes)
                    ->get();

                foreach ($contents as $item) {
                    if (! empty($item->icon) && file_exists(public_path($item->icon)) && is_file(public_path($item->icon))) {
                        $this->fileDelete($item->icon);
                    }
                    if (! empty($item->photo) && file_exists(public_path($item->photo)) && is_file(public_path($item->photo))) {
                        $this->fileDelete($item->photo);
                    }
                    $item->delete();
                }
            }

            // Delete all page records including all locales for this code
            Page::currentTheme()->where('code', $pageCode)->delete();
            Cache::pull('pages');

            $status = 'success';
            $message = __('Deleted Successfully');

            notify()->$status($message, $status);

            return redirect()->route('admin.page.create');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();

            notify()->$status($message, $status);

            return back();
        }
    }

    // ================================== Landing Section ===============================================

    public function landingSection($section)
    {
        $currentTheme = site_theme();
        $landingPage = LandingPage::currentTheme()->where('code', $section)->get();
        $engLandingPage = $landingPage->where('locale', '=', 'en')->first();
        $status = $engLandingPage->status;
        $groupData = $landingPage->groupBy('locale');

        $groupData = $groupData->map(function ($items) {
            return json_decode($items->first()->data, true);
        })->toArray();

        $languages = Language::where('status', true)->get();

        $locale = array_column($languages->toArray(), 'locale');
        $engData = json_decode($engLandingPage->data, true);
        $localeKey = array_fill_keys($locale, $engData);

        $groupData = array_merge($localeKey, $groupData);

        $landingContent = LandingContent::currentTheme()->where('type', $section)->where('locale', 'en')->get();

        return view(sprintf('backend.page.%s.section.%s', $currentTheme, $section), ['groupData' => $groupData, 'current_theme' => $currentTheme, 'languages' => $languages, 'status' => $status, 'landingContent' => $landingContent]);
    }

    public function landingSectionUpdate(Request $request)
    {
        $input = $request->all();

        if ($request->ajax()) {

            $engLandingPage = LandingPage::currentTheme()->where('code', $input['target_code'])->where('locale', 'en')->first();

            $data = json_decode($engLandingPage->data, true);

            if (file_exists(public_path($data[$input['field_name']]))) {
                @unlink(public_path($data[$input['field_name']]));

                $data = array_merge($data, [$input['field_name'] => null]);

                $update = $engLandingPage->update([
                    'data' => json_encode($data),
                ]);

                return response()->json([
                    'status' => $update,
                ]);
            }
        }

        $data = $request->except(['section_code', 'status', '_token', 'section_locale', 'deleted']);

        $sectionCode = $input['section_code'];
        $sectionlocale = $input['section_locale'];

        $engLandingPage = LandingPage::currentTheme()->where('code', $sectionCode)->where('locale', '=', 'en')->first();
        $landingPage = LandingPage::currentTheme()->where('code', $sectionCode)->where('locale', $sectionlocale)->first();

        if (! $landingPage) {
            $landingPage = $engLandingPage->replicate();
            $landingPage->locale = $sectionlocale;
            $landingPage->save();
        }

        $oldData = json_decode($landingPage->data, true);
        $engOldData = json_decode($engLandingPage->data, true);

        $uploadedImages = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $values = json_decode(data_get($engOldData, $key), true);
                $hasUploadedFile = false;
                foreach ($value as $fileKey => $file) {
                    if (is_file($file)) {
                        $values[$fileKey] = self::imageUploadTrait($file, folderPath: "landing-contents/{$key}");
                        $hasUploadedFile = true;
                    }
                }
                if ($hasUploadedFile) {
                    $uploadedImages[$key] = json_encode($values);
                }
                $data[$key] = json_encode($values);
            } elseif (is_file($value)) {
                $oldValue = Arr::get($oldData, $key);
                $data[$key] = self::imageUploadTrait($value, $oldValue, folderPath: "landing-contents/{$key}");
                $uploadedImages[$key] = $data[$key];
            }
        }

        $data = array_merge($engOldData, $data);

        // Deleted images
        $deleted = $request->get('deleted', []);
        foreach ($deleted as $key => $value) {

            $valueToArrays = json_decode($value, true);

            if (is_array($valueToArrays)) {
                foreach ($valueToArrays as $file) {
                    if (file_exists(public_path($file))) {
                        @unlink(public_path($file));
                    }
                    // Array value remove
                    $data[$key] = json_encode(array_values(array_diff(json_decode($data[$key], true), [$file])));
                }
            }
        }

        $landingPage->update([
            'data' => json_encode($data),
        ]);

        // If editing in English and images were uploaded, update all other languages with the same images
        if ($sectionlocale === 'en' && ! empty($uploadedImages)) {

            $locales = Language::where('status', true)
                ->where('locale', '!=', 'en')
                ->pluck('locale');

            $landingPages = LandingPage::currentTheme()
                ->where('code', $sectionCode)
                ->whereIn('locale', $locales)
                ->get()
                ->keyBy('locale');

            foreach ($landingPages as $landingPage) {

                $data = json_decode($landingPage->data, true) ?? [];

                // Merge images
                $data = array_replace($data, $uploadedImages);

                $landingPage->update([
                    'data' => json_encode($data),
                ]);
            }
        }

        if ($sectionlocale == 'en') {
            LandingPage::where('code', $sectionCode)->update([
                'status' => $input['status'] ?? $engLandingPage->status,
            ]);
        }

        notify()->success($landingPage->name.' '.__(' updated successfully'));

        return redirect()->back();
    }

    public function contentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $data = [
                'locale_id' => LandingContent::max('id') + 1,
                'icon' => $request->hasFile('icon') ? $request->icon : $request->class_name,
                'title' => $request->title,
                'description' => $request->get('description'),
                'photo' => $request->photo ?? null,
                'type' => $request->type,
                'theme' => site_theme(),
            ];

            if ($request->hasFile('icon')) {
                $data = array_merge($data, ['icon' => self::imageUploadTrait($request->icon)]);
            }

            if ($request->hasFile('photo')) {
                $data = array_merge($data, ['photo' => self::imageUploadTrait($request->photo)]);
            }

            LandingContent::create($data);

            $status = 'success';
            $message = __('Content added successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function contentEdit($id)
    {
        $currentTheme = site_theme();
        $languages = Language::where('status', true)->get();
        $engLandingContent = LandingContent::where('id', $id)->where('locale', 'en')->first(['id', 'icon', 'locale_id', 'title', 'description', 'photo', 'type'])->toArray();
        $landingContent = LandingContent::where('id', $id)->orWhere('locale_id', $engLandingContent['locale_id'])->get();

        $groupData = $landingContent->groupBy('locale');
        $groupData = $groupData->map(function ($items) {
            return $items->first()->only(['id', 'icon', 'title', 'description', 'type', 'photo']);
        })?->toArray();

        $locale = array_column($languages->toArray(), 'locale');
        $localeKey = array_fill_keys($locale, $engLandingContent);
        $groupData = array_merge($localeKey, $groupData);

        $html = view(sprintf('backend.page.%s.section.include.__conten_edit_render', $currentTheme), ['groupData' => $groupData, 'languages' => $languages])->render();

        return response()->json(['html' => $html]);
    }

    public function contentUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        DB::beginTransaction();

        try {
            $locale = $request->locale;
            $landingContent = LandingContent::where('locale', $locale)->where('id', $request->id)->first();
            $engLandingContent = LandingContent::where('id', $request->id)->where('locale', 'en')->first();

            if (! $landingContent) {
                $landingContent = $engLandingContent->replicate();
                $landingContent->locale = $locale;
                $landingContent->created_at = $engLandingContent->created_at;
                $landingContent->save();
            }

            $icon = $request->has('type') ? ($request->get('type', 'class') == 'image' && $request->hasFile('icon') ? $request->icon : $request->class_name) : $request->icon ?? $landingContent->icon;

            $data = [
                'icon' => $icon,
                'photo' => $request->photo ?? $landingContent->photo,
                'title' => $request->title,
                'description' => $request->get('description'),
            ];

            $uploadedImages = [];

            if ($request->hasFile('icon')) {
                $data['icon'] = self::imageUploadTrait($request->file('icon'), $landingContent->icon, folderPath: "landing-list-assets/{$landingContent->type}/icons/");
                $uploadedImages['icon'] = $data['icon'];
            }

            if ($request->hasFile('photo')) {
                $data['photo'] = self::imageUploadTrait($request->file('photo'), $landingContent->photo, folderPath: "landing-list-assets/{$landingContent->type}/photos/");
                $uploadedImages['photo'] = $data['photo'];
            }

            $landingContent->update($data);

            // If editing in English and images were uploaded, update all other languages with the same images
            if ($locale === 'en' && ! empty($uploadedImages)) {
                $locales = Language::where('status', true)
                    ->where('locale', '!=', 'en')
                    ->pluck('locale');

                $otherLandingContents = LandingContent::where('locale_id', $request->id)
                    ->whereIn('locale', $locales)
                    ->get();

                foreach ($otherLandingContents as $otherContent) {
                    $otherContent->update($uploadedImages);
                }
            }

            DB::commit();

            $status = 'success';
            $message = __('Content Updated Successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function contentDelete(Request $request)
    {
        try {
            $id = $request->id;
            $content = LandingContent::where('id', $id)->get();

            foreach ($content as $item) {
                if (file_exists(public_path($item->icon))) {
                    @unlink(public_path($item->icon));
                }
                if (file_exists(public_path($item->photo))) {
                    @unlink(public_path($item->photo));
                }
                $item->delete();
            }

            $status = 'success';
            $message = __('Content Deleted Successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    // ================================== End Landing Section ===============================================
    public function pageSetting()
    {
        $currentTheme = site_theme();

        return view(sprintf('backend.page.%s.setting', $currentTheme));
    }

    public function pageSettingUpdate(Request $request)
    {
        try {
            $input = $request->except('_token');
            foreach ($input as $key => $value) {
                if ($request->hasFile($key)) {
                    $value = self::imageUploadTrait($value, getPageSetting($key));
                }

                $this->settingUpdate($key, $value);
            }

            $status = 'success';
            $message = __('Setting updated successfully');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    private function settingUpdate($key, $value)
    {
        PageSetting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function footerContent()
    {
        $currentTheme = site_theme();
        $socials = Social::orderBy('position')->get();
        $landingPage = LandingPage::currentTheme()->where('code', 'footer')->get();
        $engLandingPage = $landingPage->where('locale', '=', 'en')->first();

        $status = $engLandingPage->status;

        $groupData = $landingPage->groupBy('locale');

        $groupData = $groupData->map(function ($items) {
            return json_decode($items->first()->data, true);
        })->toArray();

        $languages = Language::where('status', true)->get();

        $locale = array_column($languages->toArray(), 'locale');

        $engData = json_decode($engLandingPage->data, true);

        $localeKey = array_fill_keys($locale, $engData);

        $groupData = array_merge($localeKey, $groupData);

        return view(sprintf('backend.page.%s.section.footer', $currentTheme), ['groupData' => $groupData, 'socials' => $socials, 'languages' => $languages, 'status' => $status]);
    }

    public function management()
    {
        $currentTheme = site_theme();
        $sections = LandingPage::currentTheme()->where('locale', 'en')->where('code', '!=', 'footer')->orderBy('sort')->get();

        return view(sprintf('backend.page.%s.section.management', $currentTheme), ['sections' => $sections]);
    }

    public function managementUpdate(Request $request)
    {
        try {
            foreach ($request->section_order as $code => $order) {
                LandingPage::currentTheme()->where('code', $code)->update([
                    'sort' => $order,
                ]);
            }

            Cache::forget('landingSections');

            $status = 'success';
            $message = __('Section order updated successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }
}
