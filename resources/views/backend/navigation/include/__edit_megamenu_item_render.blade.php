<h3 class="title mb-4">{{ __('Edit Megamenu Item') }}</h3>

@if (setting('language_switcher', 'permission'))
    <div class="site-tab-bars mb-3">
        <ul class="nav nav-pills" id="edit-megamenu-tab" role="tablist">
            @foreach ($languages as $language)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                        id="edit-megamenu-tab-{{ $language->locale }}" data-bs-toggle="pill"
                        data-bs-target="#edit-megamenu-pane-{{ $language->locale }}" type="button" role="tab"
                        aria-controls="edit-megamenu-pane-{{ $language->locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $language->name }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="tab-content" id="edit-megamenu-tabContent">
    @foreach ($groupData as $key => $data)
        @php
            $itemData = new Illuminate\Support\Fluent($data);
        @endphp
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="edit-megamenu-pane-{{ $key }}"
            role="tabpanel" aria-labelledby="edit-megamenu-tab-{{ $key }}">
            <form action="{{ route('admin.navigation.megamenu.item.update') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <input type="hidden" name="locale" value="{{ $key }}">

                <div class="site-input-groups mb-2">
                    <label for="" class="box-input-label">{{ __('Title:') }}</label>
                    <input type="text" name="title" class="box-input mb-0" placeholder="{{ __('Title') }}"
                        value="{{ $itemData->title }}" required />
                </div>
                <div class="site-input-groups mb-2">
                    <label for="" class="box-input-label">{{ __('Description:') }}</label>
                    <textarea name="description" class="form-textarea mb-0" rows="3" placeholder="{{ __('Description') }}">{{ $itemData->description }}</textarea>
                </div>

                @if ($key == 'en')
                    <div class="site-input-groups mb-2">
                        <label for="" class="box-input-label">{{ __('Icon:') }}</label>
                        <div class="wrap-custom-file">
                            <input type="file" name="icon" id="editUploadIcon-{{ $key }}"
                                accept=".gif,.jpg,.png,.webp,.svg">
                            <label for="editUploadIcon-{{ $key }}" class="{{ $item->icon ? 'file-ok' : '' }}"
                                style="{{ $item->icon ? 'background-image: url(' . asset($item->icon) . ')' : '' }}">
                                <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                    alt="">
                                <span>{{ __('Upload Icon') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="site-input-groups mb-2">
                        <label for="" class="box-input-label">{{ __('URL Type:') }}</label>
                        <select name="url_type" class="form-select mb-0 editUrlTypeSelect">
                            <option value="page" @selected($itemData->url_type == 'page')>{{ __('Select Page') }}</option>
                            <option value="custom" @selected($itemData->url_type == 'custom')>{{ __('Custom URL') }}</option>
                        </select>
                    </div>
                    <div
                        class="site-input-groups mb-2 editPageSelectGroup {{ $itemData->url_type == 'custom' ? 'd-none' : '' }}">
                        <label for="" class="box-input-label">{{ __('Page:') }}</label>
                        <select name="page_id" class="form-select mb-0 editPageSelect">
                            <option value="">--{{ __('Select One') }}--</option>
                            @foreach ($pages as $page)
                                <option value="{{ $page->id }}" @selected($itemData->page_id == $page->id)>{{ $page->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="site-input-groups mb-2 editCustomUrlGroup {{ $itemData->url_type == 'page' ? 'd-none' : '' }}">
                        <label for="" class="box-input-label">{{ __('Custom URL:') }}</label>
                        <input type="text" name="custom_url" class="box-input mb-0 editCustomUrl"
                            placeholder="{{ __('Custom URL') }}" value="{{ $itemData->custom_url }}" />
                    </div>
                @else
                    <input type="hidden" name="url_type" value="{{ $item->page_id ? 'page' : 'custom' }}">
                    <input type="hidden" name="page_id" value="{{ $item->page_id }}">
                    <input type="hidden" name="custom_url" value="{{ $item->url }}">
                @endif

                <div
                    class="megamenu-type-1-fields {{ !$navigation->megamenu_type->isListWithPreview() ? 'd-none' : '' }}">
                    <div class="site-input-groups mb-2">
                        <label for="" class="box-input-label">{{ __('Preview Title:') }}</label>
                        <input type="text" name="preview_title" class="box-input mb-0"
                            placeholder="{{ __('Preview Title') }}" value="{{ $itemData->preview_title }}" />
                    </div>
                    <div class="site-input-groups mb-2">
                        <label for="" class="box-input-label">{{ __('Preview Description:') }}</label>
                        <textarea name="preview_description" class="form-textarea mb-0" rows="3"
                            placeholder="{{ __('Preview Description') }}">{{ $itemData->preview_description }}</textarea>
                    </div>
                    @if ($key == 'en')
                        <div class="site-input-groups mb-2">
                            <label for="" class="box-input-label">{{ __('Preview Image:') }}</label>
                            <div class="wrap-custom-file">
                                <input type="file" name="preview_image"
                                    id="editUploadPreviewImage-{{ $key }}"
                                    accept=".gif,.jpg,.png,.webp,.svg">
                                <label for="editUploadPreviewImage-{{ $key }}"
                                    class="{{ $item->preview_image ? 'file-ok' : '' }}"
                                    style="{{ $item->preview_image ? 'background-image: url(' . asset($item->preview_image) . ')' : '' }}">
                                    <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                        alt="">
                                    <span>{{ __('Upload Preview Image') }}</span>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($key == 'en')
                    <div
                        class="site-input-groups mb-2 {{ !$navigation->megamenu_type->isListWithPreview() ? 'd-none' : '' }}">
                        <label class="box-input-label" for="">{{ __('Is Featured:') }}</label>
                        <div class="switch-field">
                            <input type="radio" id="edit_is_featured_yes-{{ $key }}" name="is_featured"
                                value="1" @checked($itemData->is_featured)>
                            <label for="edit_is_featured_yes-{{ $key }}">{{ __('Yes') }}</label>
                            <input type="radio" id="edit_is_featured_no-{{ $key }}" name="is_featured"
                                value="0" @checked(!$itemData->is_featured)>
                            <label for="edit_is_featured_no-{{ $key }}">{{ __('No') }}</label>
                        </div>
                    </div>
                    <div class="site-input-groups mb-2">
                        <label class="box-input-label" for="">{{ __('Status:') }}</label>
                        <div class="switch-field">
                            <input type="radio" id="edit_status_active-{{ $key }}" name="status"
                                value="1" @checked($itemData->status == 1)>
                            <label for="edit_status_active-{{ $key }}">{{ __('Active') }}</label>
                            <input type="radio" id="edit_status_inactive-{{ $key }}" name="status"
                                value="0" @checked($itemData->status == 0)>
                            <label for="edit_status_inactive-{{ $key }}">{{ __('Inactive') }}</label>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="is_featured" value="{{ $item->is_featured ? 1 : 0 }}">
                    <input type="hidden" name="status" value="{{ $item->status }}">
                @endif

                <div class="action-btns mt-3">
                    <button type="submit" class="site-btn-sm primary-btn me-2">
                        <i data-lucide="check"></i>
                        {{ __('Update Item') }}
                    </button>
                    <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal">
                        <i data-lucide="x"></i>
                        {{ __('Close') }}
                    </button>
                </div>
            </form>
        </div>
    @endforeach
</div>

<script>
    "use strict";
    
    lucide.createIcons();

    $('.editUrlTypeSelect').on('change', function() {
        var form = $(this).closest('form');
        if ($(this).val() === 'custom') {
            form.find('.editPageSelectGroup').addClass('d-none');
            form.find('.editCustomUrlGroup').removeClass('d-none');
        } else {
            form.find('.editPageSelectGroup').removeClass('d-none');
            form.find('.editCustomUrlGroup').addClass('d-none');
        }
    });

    $('input[type="file"]').on('change', function() {
        var file = $(this),
            label = file.siblings('label'),
            labelText = label.find('span');

        if (file.get(0).files && file.get(0).files[0]) {
            var fileName = file.val().split('\\').pop();
            var tmppath = URL.createObjectURL(file.get(0).files[0]);

            label.addClass('file-ok').css('background-image', 'url(' + tmppath + ')');
            labelText.text(fileName);
        }
    });
</script>
