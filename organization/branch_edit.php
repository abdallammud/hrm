<div class="modal fade" data-bs-focus="false" id="edit_branch" tabindex="-1" role="dialog" aria-labelledby="edit_branchLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="width:500px;">
        <form class="modal-content" id="editBranchForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editBranch">Edit <?=$GLOBALS['branch_keyword']['sing'];?> Details</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="editBookForm">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="branchName4Edit"><?=$GLOBALS['branch_keyword']['sing'];?> Name</label>
                                <input type="hidden" id="branch_id" name="">
                                <input type="text"  class="form-control " id="branchName4Edit" name="branchName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="contactPhone4Edit">Phone numbers</label>
                                <input type="text" placeholder="000000000 | 0000000000 | 0000000000" name="contactPhone4Edit" class="form-control" id="contactPhone4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="contactEmail4Edit">Emails</label>
                                <input type="text" placeholder="Optional"  class="form-control" id="contactEmail4Edit" name="contactEmail4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="txtAddress4Edit">Address</label>
                                <input type="text"  class="form-control" id="txtAddress4Edit" name="txtAddress4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>