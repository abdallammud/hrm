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
            <div class="card">
                <div class="card-body">
                    <form id="editDocTypeForm">
                        <input type="hidden" id="docType_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="typeName4Edit">Type Name</label>
                                    <input type="text" class="form-control" id="typeName4Edit" name="typeName4Edit" placeholder="Enter document type name">
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
?>