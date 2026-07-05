<!-- Modal for Add New Megamenu Item -->
<div class="modal fade" id="addNewMegamenuItem" tabindex="-1" aria-labelledby="addNewMegamenuItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <form action="{{ route('admin.navigation.megamenu.item.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="navigation_id" value="{{ $navigation->id }}">
                    <div class="popup-body-text">
                        <h3 class="title mb-4">{{ __('Add New Megamenu Item') }}</h3>
                        <div class="site-input-groups mb-2">
                            <label for="" class="box-input-label">{{ __('Title:') }}</label>
                            <input type="text" name="title" class="box-input mb-0"
                                placeholder="{{ __('Title') }}" required />
                        </div>
                        <div class="site-input-groups mb-2">
                            <label for="" class="box-input-label">{{ __('Description:') }}</label>
                            <textarea name="description" class="form-textarea mb-0" rows="3" placeholder="{{ __('Description') }}"></textarea>
                        </div>
                        <div class="site-input-groups mb-2">
                            <label for="" class="box-input-label">{{ __('Icon:') }}</label>
                            <div class="wrap-custom-file">
                                <input type="file" name="icon" id="uploadIcon" accept=".gif,.jpg,.png,.webp,.svg">
                                <label for="uploadIcon">
                                    <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                        alt="">
                                    <span>{{ __('Upload Icon') }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="site-input-groups mb-2">
                            <label for="" class="box-input-label">{{ __('URL Type:') }}</label>
                            <select name="url_type" class="form-select mb-0" id="urlTypeSelect">
                                <option value="page">{{ __('Select Page') }}</option>
                                <option value="custom">{{ __('Custom URL') }}</option>
                            </select>
                        </div>
                        <div class="site-input-groups mb-2" id="pageSelectGroup">
                            <label for="" class="box-input-label">{{ __('Page:') }}</label>
                            <select name="page_id" class="form-select mb-0" id="pageSelect">
                                <option value="">--{{ __('Select One') }}--</option>
                                @foreach ($pages as $page)
                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="site-input-groups d-none mb-2" id="customUrlGroup">
                            <label for="" class="box-input-label">{{ __('Custom URL:') }}</label>
                            <input type="text" name="custom_url" class="box-input mb-0"
                                placeholder="{{ __('Custom URL') }}" />
                        </div>
                        <div
                            class="megamenu-type-1-fields {{ !$navigation->megamenu_type->isListWithPreview() ? 'd-none' : '' }}">
                            <div class="site-input-groups mb-2">
                                <label for="" class="box-input-label">{{ __('Preview Title:') }}</label>
                                <input type="text" name="preview_title" class="box-input mb-0"
                                    placeholder="{{ __('Preview Title') }}" />
                            </div>
                            <div class="site-input-groups mb-2">
                                <label for="" class="box-input-label">{{ __('Preview Description:') }}</label>
                                <textarea name="preview_description" class="form-textarea mb-0" rows="3"
                                    placeholder="{{ __('Preview Description') }}"></textarea>
                            </div>
                            <div class="site-input-groups mb-2">
                                <label for="" class="box-input-label">{{ __('Preview Image:') }}</label>
                                <div class="wrap-custom-file">
                                    <input type="file" name="preview_image" id="uploadPreviewImage"
                                        accept=".gif,.jpg,.png,.webp,.svg">
                                    <label for="uploadPreviewImage">
                                        <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                            alt="">
                                        <span>{{ __('Upload Preview Image') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="site-input-groups mb-2 {{ !$navigation->megamenu_type->isListWithPreview() ? 'd-none' : '' }}">
                            <label class="box-input-label" for="">{{ __('Is Featured:') }}</label>
                            <div class="switch-field">
                                <input type="radio" id="is_featured_yes" name="is_featured" value="1">
                                <label for="is_featured_yes">{{ __('Yes') }}</label>
                                <input type="radio" id="is_featured_no" name="is_featured" value="0" checked>
                                <label for="is_featured_no">{{ __('No') }}</label>
                            </div>
                        </div>
                        <div class="site-input-groups mb-2">
                            <label class="box-input-label" for="">{{ __('Status:') }}</label>
                            <div class="switch-field">
                                <input type="radio" id="status_active" name="status" checked=""
                                    value="1">
                                <label for="status_active">{{ __('Active') }}</label>
                                <input type="radio" id="status_inactive" name="status" value="0">
                                <label for="status_inactive">{{ __('Inactive') }}</label>
                            </div>
                        </div>
                        <div class="action-btns">
                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                <i data-lucide="check"></i>
                                {{ __('Add Item') }}
                            </button>
                            <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal">
                                <i data-lucide="x"></i>
                                {{ __('Close') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
