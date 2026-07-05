<div class="modal fade" id="addNewFaq" tabindex="-1" aria-labelledby="addNewFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
                <div class="popup-body-text">
                    <h3 class="title mb-4">{{ __('Add New FAQ') }}</h3>
                    <form action="{{ route('admin.page.content-store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="service-{{ $code }}-faqs">
                        
                        <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('Question:') }}</label>
                            <input type="text" name="title" class="box-input mb-0"
                                placeholder="{{ __('Question') }}" required="" />
                        </div>

                        <div class="site-input-groups mb-0">
                            <label for="" class="box-input-label">{{ __('Answer:') }}</label>
                            <textarea name="description" class="form-textarea" placeholder="{{ __('Answer') }}"></textarea>
                        </div>

                        <div class="action-btns">
                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                <i data-lucide="check"></i>
                                {{ __('Add New') }}
                            </button>
                            <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal" aria-label="Close">
                                <i data-lucide="x"></i>
                                {{ __('Close') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
