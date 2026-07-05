<form action="{{ route('admin.settings.plugin.update', $plugin->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <h3 class="title mb-4">{{ __('Update') . ' ' . $plugin->name }}</h3>
    @php
        $pluginData = json_decode($plugin->data);
    @endphp
    @foreach (collect($pluginData)->except('currencies', 'reloadly_conversion_rate') as $key => $value)
        @if (is_string($value) && $key != 'upload_account_json')
            <div class="site-input-groups">
                <label for="" class="box-input-label">
                    {{ ucwords(str_replace('_', ' ', $key)) }}
                    @if ($key == 'site_key')
                        <i data-lucide="info" data-bs-toggle="tooltip"
                            data-bs-original-title="Note: Before add reCaptcha select v2 in reCaptcha dashboard."></i>
                    @endif
                </label>
                <input type="text" name="data[{{ $key }}]" class="box-input mb-0"
                    id="plugin-value-{{ $key }}" value="{{ $value }}" required="" />
            </div>
        @elseif(is_object($value))
            <div class="site-input-groups">
                <label for="" class="box-input-label">
                    {{ ucwords(str_replace('_', ' ', $key)) }}
                </label>
                <div class="ms-2">
                    @foreach ($value as $k => $v)
                        <br>
                        <label for="">{{ $k }}</label>
                        <input type="text" name="data[{{ $key }}][{{ $k }}]"
                            class="box-input mb-0" value="{{ $value->{$k} }}" required="" />
                    @endforeach
                </div>
            </div>
        @elseif($key == 'upload_account_json')
            <div class="site-input-groups">
                <label for="" class="box-input-label">
                    {{ ucwords(str_replace('_', ' ', $key)) }}
                </label>
                <div class="wrap-custom-file">
                    <input type="file" name="data[{{ $key }}]" id="{{ $key }}" accept=".json" />
                    <label for="{{ $key }}" class="file-ok">
                        <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}" alt="" />
                        <span>{{ blank($value) ? __('Upload') : basename($value) }}</span>
                    </label>
                </div>
            </div>
            <script>
                imagePreview();
            </script>
        @endif
    @endforeach

    @if ($plugin->name === 'Reloadly Gift Card')
        <div class="site-input-groups">
            <label class="box-input-label" for="">{{ __('Conversion Rate') }}</label>
            <div class="input-group joint-input">
                <span class="input-group-text">1 {{ setting('site_currency', 'global') }}</span>
                <input type="text" name="data[reloadly_conversion_rate]" data-validate="decimal"
                    value="{{ data_get($pluginData, 'reloadly_conversion_rate') }}" class="form-control">
                <span class="input-group-text"
                    id="reloadly-account-currency">{{ data_get($pluginData, 'account_currency') }}</span>
            </div>
        </div>
    @endif

    <div class="site-input-groups">
        <label class="box-input-label" for="">{{ __('Status:') }}</label>
        <div class="switch-field">
            <input type="radio" id="plugin-status" name="status" value="1"
                @if ($plugin->status) checked @endif />
            <label for="plugin-status">{{ __('Active') }}</label>
            <input type="radio" id="plugin-status-no" name="status" value="0"
                @if (!$plugin->status) checked @endif />
            <label for="plugin-status-no">{{ __('Deactivated') }}</label>
        </div>
    </div>

    <div class="action-btns">
        <button type="submit" class="site-btn-sm primary-btn me-2">
            <i data-lucide="check"></i>
            {{ __(' Save Changes') }}
        </button>
        <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal" aria-label="Close">
            <i data-lucide="x"></i>
            {{ __('Close') }}
        </a>
    </div>
</form>

<script>
    "use strict";
    $('#plugin-value-account_currency').on('input', function() {
        console.log(this.value);
        $('#reloadly-account-currency').text(this.value);
    });
</script>
