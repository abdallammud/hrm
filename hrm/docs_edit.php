<?php
?>

<!-- Edit Folder Modal -->
<div class="modal fade" id="edit_folder" tabindex="-1" aria-labelledby="editFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFolderModalLabel">Edit Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFolderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editFolderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="editFolderName" name="name" required>
                        <input type="hidden" id="editFolderId" name="id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Document Type Modal -->
<div class="modal fade" id="edit_docType" tabindex="-1" aria-labelledby="editDocTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocTypeModalLabel">Edit Document Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDocTypeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDocTypeName" class="form-label">Document Type Name</label>
                        <input type="text" class="form-control" id="editDocTypeName" name="name" required>
                        <input type="hidden" id="editDocTypeId" name="id">
                    </div>
                    <div class="mb-3">
                        <label for="editDocTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDocTypeDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
?>