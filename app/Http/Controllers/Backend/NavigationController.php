<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MegamenuType;
use App\Enums\NavigationType;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MegamenuItem;
use App\Models\Navigation;
use App\Models\Page;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class NavigationController extends Controller implements HasMiddleware
{
    use ImageUpload;

    public static function middleware()
    {
        return [
            new Middleware('permission:navigation-manage'),
        ];
    }

    public function index()
    {
        $pages = Page::where('locale', 'en')->where('status', true)->where('theme', site_theme())->get();
        $navigations = Navigation::all();

        return view('backend.navigation.menu', compact('pages', 'navigations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'select_page' => 'required',
            'custom_url' => 'required_if:select_page,custom',
            'type' => 'required|array',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }
        $input = $request->all();
        $url = $input['custom_url'];
        $pageId = $input['select_page'];
        if ($input['select_page'] != 'custom') {
            $page = Page::find($input['select_page']);
            $url = $page->url;
        } else {
            $pageId = null;
        }

        $data = [
            'name' => $input['name'],
            'megamenu_name' => $input['megamenu_name'] ?? null,
            'page_id' => $pageId,
            'url' => $url,
            'type' => $request->get('type', []),
            'megamenu_type' => $request->get('megamenu_type', MegamenuType::ListWithPreview),
            'status' => $input['status'],
        ];

        if ($input['type'] == NavigationType::Header->value) {
            $headerPosition = Navigation::max('header_position');
            $data = array_merge($data, ['header_position' => $headerPosition + 1]);
        } else {
            $footerPosition = Navigation::max('footer_position');
            $data = array_merge($data, ['footer_position' => $footerPosition + 1]);
        }

        Navigation::create($data);

        notify()->success(__('New Menu Created Successfully'));

        return redirect()->back();
    }

    public function edit($id)
    {

        $navigation = Navigation::find($id);
        $pages = Page::where('locale', 'en')->where('status', true)->where('theme', site_theme())->get();

        return view('backend.navigation.include.__edit_section', compact('navigation', 'pages'))->render();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $navigation = Navigation::find($id);

        // Delete all megamenu items associated with this navigation
        if ($navigation) {
            $megamenuItems = $navigation->megamenuItems;
            foreach ($megamenuItems as $item) {
                if ($item->featured_image) {
                    $this->fileDelete($item->featured_image);
                }
                $item->delete();
            }
        }

        $navigation->delete();
        notify()->success(__('Menu Delete Successfully'));

        return redirect()->back();
    }

    public function header()
    {
        $navigations = Navigation::whereJsonContains('type', 'header')->orderBy('header_position')->get();

        return view('backend.navigation.header', compact('navigations'));
    }

    public function footer()
    {
        $navigations = Navigation::whereJsonContains('type', 'widget_one')->orWhereJsonContains('type', 'widget_two')->orWhereJsonContains('type', 'widget_three')->orderBy('footer_position')->get();

        return view('backend.navigation.footer', compact('navigations'));
    }

    public function positionUpdate(Request $request)
    {
        $inputs = $request->except('_token', 'type');
        $type = $request->type;

        $navigationInstance = new Navigation;
        $i = 1;

        foreach ($inputs as $input) {
            $navigation = $navigationInstance->find((int) $input);

            if ($type == 'header') {
                $navigation->update([
                    'header_position' => $i,
                ]);
            } else {
                $navigation->update([
                    'footer_position' => $i,
                ]);
            }

            $i++;
        }

        notify()->success(__('Menu Draggable Successfully'));

        return redirect()->back();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'select_page' => 'nullable',
            'custom_url' => 'required_if:select_page,custom',
            'type' => 'required|array',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }
        $input = $request->all();
        $url = $input['custom_url'];
        $pageId = $input['select_page'];
        if ($input['select_page'] != 'custom') {
            $page = Page::find($input['select_page']);
            $url = $page->url;
        } else {
            $pageId = null;
        }

        $data = [
            'name' => $input['name'],
            'megamenu_name' => $input['megamenu_name'] ?? null,
            'page_id' => $pageId,
            'url' => $url,
            'type' => $request->get('type', []),
            'megamenu_type' => $request->get('megamenu_type', MegamenuType::ListWithPreview),
            'status' => $input['status'],
        ];

        // Add has_megamenu if provided
        if ($request->has('has_megamenu')) {
            $data['has_megamenu'] = $request->has_megamenu ? 1 : 0;
        }

        $navigation = Navigation::find($input['id']);

        if ($input['type'] != $navigation->type && $input['type'] == NavigationType::Header->value) {
            $headerPosition = Navigation::max('header_position');
            $footerPosition = Navigation::max('footer_position');
            $data = array_merge($data, ['header_position' => $headerPosition + 1, 'footer_position' => $footerPosition + 1]);
        } elseif ($input['type'] != $navigation->type && $input['type'] == NavigationType::WidgetOne->value || $input['type'] == NavigationType::WidgetTwo->value) {
            $footerPosition = Navigation::max('footer_position');
            $data = array_merge($data, ['footer_position' => $footerPosition + 1, 'header_position' => null]);
        }

        Navigation::find($input['id'])->update($data);

        notify()->success(__('Menu updated successfully!'));

        return redirect()->back();
    }

    public function typeDelete($id, $type)
    {
        $navigation = Navigation::find($id);

        if ($navigation->type == $type) {
            notify()->error('This Menu Only Available.'.ucwords($type).' Position', 'Can Not Delete');

            return redirect()->back();
        }

        if ($type == 'header') {
            $navigation->update([
                'header_position' => null,
                'type' => NavigationType::Footer->value,
            ]);
        } else {
            $navigation->update([
                'footer_position' => null,
                'type' => NavigationType::Header->value,
            ]);
        }

        notify()->success(__('Menu deleted successfully!'));

        return redirect()->back();
    }

    public function translate($id)
    {
        $navigation = Navigation::find($id);
        $languages = Language::where('status', true)->get();

        $locale = array_column($languages->toArray(), 'locale');

        $navigationContent = $navigation->translate == null ? [] : json_decode($navigation->translate, true);

        $localeKey = array_fill_keys($locale, [
            'name' => $navigation->name,
            'megamenu_name' => $navigation->megamenu_name,
        ]);

        foreach ($navigationContent as $key => $content) {
            if (! is_array($content)) {
                $navigationContent[$key] = [
                    'name' => $content,
                    'megamenu_name' => '',
                ];
            }
        }

        $localeContent = array_merge($localeKey, $navigationContent);

        return view('backend.navigation.translate', compact('languages', 'navigation', 'localeContent'));
    }

    public function translateNow(Request $request)
    {
        $input = $request->all();

        $navigation = Navigation::find($input['id']);

        $oldTranslate = $navigation->translate == null ? [] : json_decode($navigation->translate, true);

        $value = [];
        $value[$input['locale']] = [
            'name' => $input['name'],
            'megamenu_name' => $input['megamenu_name'],
        ];

        $result = array_merge($oldTranslate, $value);

        $navigation->update([
            'translate' => json_encode($result),
        ]);

        notify()->success(__('Menu translate successfully!'));

        return redirect()->back();
    }

    public function megamenu($id)
    {
        $navigation = Navigation::findOrFail($id);
        $megamenuItems = $navigation->megamenuItems;
        $pages = Page::where('locale', 'en')->where('status', true)->where('theme', site_theme())->get();

        // If item_id is provided, return rendered HTML for editing
        if (request()->has('item_id')) {
            $item = MegamenuItem::findOrFail(request()->item_id);
            $languages = Language::where('status', true)->get();
            $pages = Page::where('locale', 'en')->where('status', true)->where('theme', site_theme())->get();

            // Prepare translation data
            $translate = $item->translate == null ? [] : json_decode($item->translate, true);
            $locale = array_column($languages->toArray(), 'locale');

            $itemData = [
                'id' => $item->id,
                'icon' => $item->icon,
                'title' => $item->title,
                'description' => $item->description,
                'url_type' => $item->page_id ? 'page' : 'custom',
                'page_id' => $item->page_id,
                'custom_url' => $item->url,
                'preview_title' => $item->preview_title,
                'preview_description' => $item->preview_description,
                'preview_image' => $item->preview_image,
                'is_featured' => $item->is_featured,
                'status' => $item->status,
            ];

            $localeKey = array_fill_keys($locale, $itemData);
            $groupData = array_merge($localeKey, $translate);

            $html = view('backend.navigation.include.__edit_megamenu_item_render', compact('groupData', 'languages', 'pages', 'navigation', 'item'))->render();

            return response()->json(['html' => $html]);
        }

        return view('backend.navigation.megamenu', compact('navigation', 'megamenuItems', 'pages'));
    }

    public function megamenuItemStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'navigation_id' => 'required|exists:navigations,id',
            'title' => 'required',
            'description' => 'nullable|string',
            'icon' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'url_type' => 'required|in:page,custom',
            'page_id' => 'required_if:url_type,page|exists:pages,id',
            'custom_url' => 'required_if:url_type,custom|nullable|string',
            'featured_image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $data = [
            'navigation_id' => $request->navigation_id,
            'title' => $request->title,
            'description' => $request->description,
            'preview_title' => $request->preview_title,
            'preview_description' => $request->preview_description,
            'is_featured' => $request->is_featured ?? false,
            'status' => $request->status,
        ];

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->imageUploadTrait($request->icon, null, 'megamenu/icons');
        }

        // Handle URL - either page or custom
        if ($request->url_type == 'page') {
            $page = Page::find($request->page_id);
            $data['page_id'] = $request->page_id;
            $data['url'] = $page ? $page->url : null;
        } else {
            $data['url'] = $request->custom_url;
            $data['page_id'] = null;
        }

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $data['preview_image'] = $this->imageUploadTrait($request->preview_image, null, 'megamenu');
        }

        // Set sort order
        $maxOrder = MegamenuItem::where('navigation_id', $request->navigation_id)->max('sort_order');
        $data['sort_order'] = ($maxOrder ?? 0) + 1;

        MegamenuItem::create($data);

        // Auto-enable megamenu on navigation
        Navigation::find($request->navigation_id)->update(['has_megamenu' => true]);

        notify()->success(__('Megamenu item created successfully'));

        return redirect()->back();
    }

    public function megamenuItemUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:megamenu_items,id',
            'title' => 'required',
            'description' => 'nullable|string',
            'icon' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'url_type' => 'required|in:page,custom',
            'page_id' => 'required_if:url_type,page|exists:pages,id',
            'custom_url' => 'required_if:url_type,custom|nullable|string',
            'featured_image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $item = MegamenuItem::findOrFail($request->id);
        $locale = $request->get('locale', 'en');

        if ($locale == 'en') {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'preview_title' => $request->preview_title,
                'preview_description' => $request->preview_description,
                'is_featured' => $request->is_featured ?? false,
                'status' => $request->status,
            ];

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $data['icon'] = $this->imageUploadTrait($request->icon, $item->icon, 'megamenu/icons');
            }

            // Handle URL - either page or custom
            if ($request->url_type == 'page') {
                $page = Page::find($request->page_id);
                $data['page_id'] = $request->page_id;
                $data['url'] = $page ? $page->url : null;
            } else {
                $data['url'] = $request->custom_url;
                $data['page_id'] = null;
            }

            // Handle preview image upload
            if ($request->hasFile('preview_image')) {
                $data['preview_image'] = $this->imageUploadTrait($request->preview_image, $item->preview_image, 'megamenu');
            }

            $item->update($data);

            // Ensure at least one featured item per navigation
            if (isset($data['is_featured']) && $data['is_featured']) {
                MegamenuItem::where('id', '!=', $item->id)
                    ->where('navigation_id', $item->navigation_id)
                    ->update(['is_featured' => false]);
            }
        } else {
            // Update translation
            $oldTranslate = $item->translate == null ? [] : json_decode($item->translate, true);
            $value = [];
            $value[$locale] = [
                'title' => $request->title,
                'description' => $request->description,
                'preview_title' => $request->preview_title,
                'preview_description' => $request->preview_description,
            ];

            $result = array_merge($oldTranslate, $value);
            $item->update([
                'translate' => json_encode($result),
            ]);
        }

        notify()->success(__('Megamenu item updated successfully'));

        return redirect()->back();
    }

    public function megamenuItemDelete(Request $request)
    {
        $item = MegamenuItem::findOrFail($request->id);
        $navigationId = $item->navigation_id;

        // Delete icon if exists
        if ($item->icon) {
            $this->fileDelete($item->icon);
        }

        // Delete preview image if exists
        if ($item->preview_image) {
            $this->fileDelete($item->preview_image);
        }

        $item->delete();

        // Auto-disable megamenu if no items left
        $navigation = Navigation::find($navigationId);
        if ($navigation && $navigation->megamenuItems->count() == 0) {
            $navigation->update(['has_megamenu' => false]);
        }

        notify()->success(__('Megamenu item deleted successfully'));

        return redirect()->back();
    }

    public function megamenuItemPositionUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'exists:megamenu_items,id',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $items = $request->input('items', []);

        foreach ($items as $index => $itemId) {
            MegamenuItem::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        notify()->success(__('Position updated successfully'));

        return redirect()->back();
    }
}
