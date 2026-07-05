<h3 class="title">{{ __('Edit Content') }}</h3>

@if (setting('language_switcher', 'permission'))
    <div class="site-tab-bars">
        <ul class="nav nav-pills" id="edit-tab" role="tablist">
            @foreach ($languages as $language)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="edit-tab-{{ $language->locale }}"
                        data-bs-toggle="pill" data-bs-target="#edit-pane-{{ $language->locale }}" type="button"
                        role="tab" aria-controls="edit-pane-{{ $language->locale }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i data-lucide="languages"></i> {{ $language->name }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
@endif


<div class="tab-content" id="edit-tabContent">
    @foreach ($groupData as $key => $landingContent)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="edit-pane-{{ $key }}"
            role="tabpanel" aria-labelledby="edit-tab-{{ $key }}">
            <div class="row">
                <div class="col-xl-12">
                    <form action="{{ route('admin.page.content-update') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="id" value="{{ $landingContent['id'] }}">
                        <input type="hidden" name="locale" value="{{ $key }}">

                        {{-- EN only --}}
                        @if ($key === 'en')
                            @if (in_array($landingContent['type'], [
                                    'agent',
                                    'merchant',
                                    'about',
                                    'features',
                                    'solutions',
                                    'mobile-recharge-how-it-works',
                                    'mobile-recharge-features',
                                    'virtual-cards-how-it-works',
                                    'virtual-cards-features',
                                    'bill-payment-categories',
                                    'bill-payment-features',
                                ]) || str_contains($landingContent['type'], '-features'))
                                <div class="site-input-groups">
                                    <label class="box-input-label">{{ __('Icon') }}</label>
                                    <div class="wrap-custom-file">
                                        <input type="file" name="icon" id="uploadIcon-{{ $key }}"
                                            accept=".gif,.jpg,.png,.webp">
                                        <label for="uploadIcon-{{ $key }}" class="file-ok"
                                            style="background-image:url({{ asset($landingContent['icon']) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}">
                                            <span>{{ __('Upload') }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="site-input-groups">
                            <label class="box-input-label">
                                {{ in_array($landingContent['type'], ['faqs', 'bill-payment-faqs', 'mobile-recharge-faqs', 'virtual-cards-faqs']) ? __('Question') : __('Title') }}
                            </label>
                            <input type="text" name="title" value="{{ $landingContent['title'] }}"
                                class="box-input mb-0" required>
                        </div>

                        <div class="site-input-groups mb-0">
                            <label class="box-input-label">
                                @if ($landingContent['type'] === 'counter')
                                    {{ __('Number') }}
                                @elseif (in_array($landingContent['type'], ['faqs', 'bill-payment-faqs', 'mobile-recharge-faqs', 'virtual-cards-faqs']))
                                    {{ __('Answer') }}
                                @else
                                    {{ __('Description') }}
                                @endif
                            </label>
                            <textarea name="description" class="form-textarea" placeholder="Description">{{ $landingContent['description'] }}</textarea>
                        </div>

                        <div class="action-btns">
                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                <i data-lucide="check"></i> {{ __('Save Changes') }}
                            </button>
                            <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal">
                                <i data-lucide="x"></i> {{ __('Close') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>


<script>
    $('#uploadPhoto').on('change', function() {
        filePreview($(this), 'label[for=uploadPhoto]');
    });

    $('#uploadIcon').on('change', function() {
        filePreview($(this), 'label[for=uploadIcon]');
    })

    function filePreview(el, target) {
        // Refs
        var file = $(el),
            label = $(target),
            labelText = label.find('span');

        var fileName = file.val().split('\\').pop();
        var tmppath = URL.createObjectURL(file.get(0).files[0]);

        label.css('background-image', 'url(' + tmppath + ')');
        labelText.text(fileName);
    }

    $('#editIconTypes').on('change', function() {
        initIconType();
    });
</script>
