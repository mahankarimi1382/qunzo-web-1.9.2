@extends('backend.navigation.index')
@section('title')
    {{ __('Megamenu Management') }} - {{ $navigation->name }}
@endsection
@section('navigation_content')
    <div class="col-xl-12">
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Megamenu Items for') }}: <strong>{{ $navigation->name }}</strong></h3>
                <div class="card-header-links">
                    <a href="{{ route('admin.navigation.menu') }}" class="card-header-link">
                        <i data-lucide="arrow-left"></i> {{ __('Back to Navigations') }}
                    </a>
                    <a href="" class="card-header-link" type="button" data-bs-toggle="modal"
                        data-bs-target="#addNewMegamenuItem">{{ __('Add New Item') }}</a>
                </div>
            </div>
            <form action="{{ route('admin.navigation.megamenu.item.position.update') }}" method="post">
                @csrf
                <div class="site-card-body">
                    <p class="paragraph"><i data-lucide="alert-triangle"></i>{{ __('All the') }}
                        <strong>{{ __('Menu Items are Draggable.') }}</strong> {{ __('Once you drag then click') }}
                        <strong>{{ __('Save Changes') }}</strong>
                    </p>
                    <div class="site-table table-responsive mb-0">
                        <table class="table mb-0" id="sortable">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Icon') }}</th>
                                    <th scope="col">{{ __('Title') }}</th>
                                    <th scope="col">{{ __('Description') }}</th>
                                    @if ($navigation->megamenu_type->isListWithPreview())
                                        <th scope="col">{{ __('Preview') }}</th>
                                        <th scope="col">{{ __('Featured') }}</th>
                                    @endif
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($megamenuItems as $item)
                                    <tr>
                                        <input type="hidden" name="items[]" value="{{ $item->id }}">
                                        <td>
                                            @if ($item->icon)
                                                <img src="{{ asset($item->icon) }}" alt="Icon"
                                                    style="max-width: 30px; max-height: 30px;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->title }}</strong>
                                        </td>
                                        <td>{{ Str::limit($item->description, 50) }}</td>
                                        @if ($navigation->megamenu_type->isListWithPreview())
                                            <td>
                                                @if ($item->preview_image)
                                                    <img src="{{ asset($item->preview_image) }}" alt="Preview"
                                                        style="max-width: 30px; max-height: 30px;">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->is_featured)
                                                    <div class="site-badge success">{{ __('Yes') }}</div>
                                                @else
                                                    <div class="site-badge pending">{{ __('No') }}</div>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if ($item->status)
                                                <div class="site-badge success">{{ __('Active') }}</div>
                                            @else
                                                <div class="site-badge pending">{{ __('Inactive') }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="round-icon-btn primary-btn editMegamenuItem" type="button"
                                                data-id="{{ $item->id }}">
                                                <i data-lucide="edit-3"></i>
                                            </button>
                                            <button class="round-icon-btn red-btn deleteMegamenuItem" type="button"
                                                data-id="{{ $item->id }}">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No Megamenu Items Found!') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="site-card-footer">
                    <button type="submit" class="site-btn-sm primary-btn">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    @include('backend.navigation.include.__add_megamenu_item')
    @include('backend.navigation.include.__edit_megamenu_item')
    @include('backend.navigation.include.__delete_megamenu_item')
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            "use strict";

            // Initialize image preview
            imagePreview();

            // File preview handlers
            $('#uploadIcon').on('change', function() {
                filePreview($(this), 'label[for=uploadIcon]');
            });

            $('#uploadPreviewImage').on('change', function() {
                filePreview($(this), 'label[for=uploadPreviewImage]');
            });

            $('#editUploadPreviewImage').on('change', function() {
                filePreview($(this), '#editPreviewImageLabel');
            });

            $('#editUploadFeaturedImage').on('change', function() {
                filePreview($(this), '#editFeaturedImageLabel');
            });

            function filePreview(el, target) {
                var file = $(el),
                    label = $(target),
                    labelText = label.find('span');

                if (file.get(0).files && file.get(0).files[0]) {
                    var fileName = file.val().split('\\').pop();
                    var tmppath = URL.createObjectURL(file.get(0).files[0]);

                    label.addClass('file-ok').css('background-image', 'url(' + tmppath + ')');
                    labelText.text(fileName);
                }
            }

            // URL Type Toggle
            $('#urlTypeSelect').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#pageSelectGroup').addClass('d-none');
                    $('#customUrlGroup').removeClass('d-none');
                    $('#pageSelect').prop('required', false);
                    $('#customUrlGroup input').prop('required', true);
                } else {
                    $('#pageSelectGroup').removeClass('d-none');
                    $('#customUrlGroup').addClass('d-none');
                    $('#pageSelect').prop('required', true);
                    $('#customUrlGroup input').prop('required', false);
                }
            });

            $('#editUrlTypeSelect').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#editPageSelectGroup').addClass('d-none');
                    $('#editCustomUrlGroup').removeClass('d-none');
                } else {
                    $('#editPageSelectGroup').removeClass('d-none');
                    $('#editCustomUrlGroup').addClass('d-none');
                }
            });

            // Edit Megamenu Item
            $('.editMegamenuItem').on('click', function() {
                var id = $(this).data('id');
                $.get('{{ route('admin.navigation.megamenu', $navigation->id) }}?item_id=' + id, function(
                    data) {
                    // Handle the response data
                });
            });

            // Delete Megamenu Item
            $('.deleteMegamenuItem').on('click', function() {
                var id = $(this).data('id');
                $('#deleteItemId').val(id);
                $('#deleteMegamenuItem').modal('show');
            });

            // Initialize sortable
            $("#sortable tbody").sortable({
                cursor: "move",
                placeholder: "sortable-placeholder",
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        // Set helper cell sizes to match the original sizes
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                }
            }).disableSelection();

            // Load item data for editing
            $('.editMegamenuItem').on('click', function() {
                var id = $(this).data('id');
                $.get('{{ route('admin.navigation.megamenu', $navigation->id) }}', {
                    item_id: id
                }, function(data) {
                    $('#edit-megamenu-item-content').html(data.html);
                    $('#editMegamenuItem').modal('show');
                }, 'json');
            });
        });
    </script>
@endsection
