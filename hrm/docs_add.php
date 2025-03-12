<!-- Add folder -->
<div class="modal fade" data-bs-focus="false" id="add_folder" tabindex="-1" role="dialog" aria-labelledby="add_folderLabel" aria-hidden="true">
    <div class="modal-dialog" role="folder" style="width:500px;">
        <form class="modal-content" id="addFolderForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add Folder</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="folderName">Folder Name</label>
                                <input type="text" class="form-control validate" data-msg="Folder name is required" id="folderName" name="folderName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Document Type Modal -->
<div class="modal fade" id="add_docType" tabindex="-1" aria-labelledby="addDocTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocTypeModalLabel">Add Document Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDocTypeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="docTypeName" class="form-label">Document Type Name</label>
                        <input type="text" class="form-control" id="docTypeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="docTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="docTypeDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>