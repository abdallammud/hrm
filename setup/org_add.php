<div class="modal fade" data-bs-focus="false" id="add_org" tabindex="-1" role="dialog" aria-labelledby="add_orgLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="width:500px;">
        <form class="modal-content" id="addOrgForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editBookLabel">Add Organization</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="editBookForm">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="orgName">Organizaion Name</label>
                                <input type="text"  class="form-control " id="orgName" name="orgName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="contactPhone">Phone numbers</label>
                                <input type="text" placeholder="000000000 | 0000000000 | 0000000000" name="contactPhone" class="form-control" id="contactPhone">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="contactEmail">Emails</label>
                                <input type="text" placeholder="Optional"  class="form-control" id="contactEmail" name="contactEmail">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="txtAddress">Address</label>
                                <input type="text"  class="form-control" id="txtAddress" name="txtAddress">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>