<a href="{{ route('admin.template.edit',$id) }}" class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
   title="{{ __('Edit Template') }}" data-bs-original-title="{{ __('Edit Template') }}"><i data-lucide="edit-3"></i></a>
<a target="_blank" href="{{ route('admin.template.preview',$id) }}" class="round-icon-btn blue-btn" data-bs-toggle="tooltip"
   title="{{ __('Preview Template') }}" data-bs-original-title="{{ __('Preview Template') }}"><i data-lucide="eye"></i></a>
<script>
   'use strict';
   lucide.createIcons();
</script>
