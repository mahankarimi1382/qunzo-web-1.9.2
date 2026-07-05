<!-- Modal for Edit Megamenu Item -->
<div class="modal fade" id="editMegamenuItem" tabindex="-1" aria-labelledby="editMegamenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <form action="{{ route('admin.navigation.megamenu.item.update') }}" method="post"
                    enctype="multipart/form-data" id="editMegamenuItemForm">
                    @csrf
                    <input type="hidden" name="id" id="editItemId">
                    <div class="popup-body-text" id="edit-megamenu-item-content">
                        {{-- Rendered Content will be placed here --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
