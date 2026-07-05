<form action="{{ route('admin.navigation.menu.update') }}" method="post">
    @csrf
    <input type="hidden" name="id" id="manuId" value="{{ $navigation->id }}">
    <h3 class="title mb-4">{{ __('Update Menu Item') }}</h3>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Menu Name:') }}</label>
        <input type="text" name="name" class="box-input mb-0 name" placeholder="Menu Name"
            value="{{ $navigation->name }}" required="" />
    </div>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Megamenu Title:') }}</label>
        <input type="text" name="megamenu_name" class="box-input mb-0" placeholder="Megamenu Title"
            value="{{ $navigation->megamenu_name }}" />
    </div>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Page:') }}</label>
        <select name="select_page" class="form-select edit-page-select">
            <option value="">--{{ __('Select One') }}--</option>
            @foreach ($pages as $page)
                <option @selected($page->id == $navigation->page_id) value="{{ $page->id }}">{{ $page->title }}</option>
            @endforeach
            <option value="custom" @selected($navigation->page_id == null)>{{ __('Custom Url') }}</option>
        </select>
    </div>
    <div class="site-input-groups edit-custom-url-input @if ($navigation->page_id) hidden @endif">
        <label for="" class="box-input-label">{{ __('Custom URL:') }}</label>
        <input type="text" name="custom_url" class="box-input mb-0 custom-url" placeholder="Custom URL"
            value="{{ $navigation->url }}" />
    </div>

    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Display In:') }}</label>
        <select name="type[]" class="form-select type" id="editType" multiple>
            <option value="header" @selected(in_array('header', data_get($navigation, 'type', [])))>{{ __('Header') }}</option>
            <option value="widget_one" @selected(in_array('widget_one', data_get($navigation, 'type', [])))>{{ __('Footer Widget One') }}</option>
            <option value="widget_two" @selected(in_array('widget_two', data_get($navigation, 'type', [])))>{{ __('Footer Widget Two') }}</option>
            <option value="widget_three" @selected(in_array('widget_three', data_get($navigation, 'type', [])))>{{ __('Footer Widget Three') }}</option>
        </select>
    </div>

    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Megamenu Type:') }}</label>
        <select name="megamenu_type" class="form-select">
            <option value="1" @selected($navigation->megamenu_type === \App\Enums\MegamenuType::ListWithPreview)>{{ __('Type 1 (List with Preview)') }}</option>
            <option value="2" @selected($navigation->megamenu_type === \App\Enums\MegamenuType::Grid)>{{ __('Type 2 (Multi-column List)') }}</option>
        </select>
    </div>

    <div class="site-input-groups">
        <label class="box-input-label" for="">{{ __('Status:') }}</label>
        <div class="switch-field">
            <input type="radio" id="active{{ $navigation->id }}" name="status" @checked($navigation->status)
                value="1">
            <label for="active{{ $navigation->id }}">{{ __('Active') }}</label>
            <input type="radio" id="disabled{{ $navigation->id }}" name="status" @checked(!$navigation->status)
                value="0">
            <label for="disabled{{ $navigation->id }}">{{ __('Disabled') }}</label>
        </div>
    </div>

    <div class="action-btns">
        <button type="submit" class="site-btn-sm primary-btn me-2">
            <i data-lucide="check"></i>
            {{ __('Update Menu') }}
        </button>
        <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal">
            <i data-lucide="x"></i>
            {{ __('Close') }}
        </a>
    </div>
</form>
