<h3 class="title">{{ __('Edit Testimonial') }}</h3>

<form action="{{ route('admin.page.testimonial.update', $testimonial->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Thumbnail') }}</label>
        <div class="wrap-custom-file">
            <input type="file" name="picture" id="editPicture" accept=".gif, .jpg, .png" />
            <label for="editPicture" class="file-ok" style="background-image: url({{ asset($testimonial->picture) }})">
                <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}" alt="" />
                <span>{{ __('Upload') }}</span>
            </label>
        </div>
    </div>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Name:') }}</label>
        <input type="text" name="name" class="box-input mb-0" placeholder="{{ __('Name') }}"
            value="{{ $testimonial->name }}" required="" />
    </div>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Designation:') }}</label>
        <input type="text" name="designation" class="box-input mb-0" placeholder="{{ __('Designation') }}"
            value="{{ $testimonial->designation }}" required="" />
    </div>
    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Message:') }}</label>
        <textarea name="message" class="form-textarea mb-0" placeholder="{{ __('Message') }}">{{ $testimonial->message }}</textarea>
    </div>

    <div class="action-btns">
        <button type="submit" class="site-btn-sm primary-btn me-2">
            <i data-lucide="check"></i>
            {{ __('Update') }}
        </button>
        <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal" aria-label="Close">
            <i data-lucide="x"></i>
            {{ __('Close') }}
        </a>
    </div>
</form>
