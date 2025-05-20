<!-- Edit desination -->
<div class="modal  fade"  data-bs-focus="false" id="edit_designation" tabindex="-1" role="dialog" aria-labelledby="edit_designationLabel" aria-hidden="true">
    <div class="modal-dialog" role="designation" style="width:500px;">
        <form class="modal-content" id="editDesignationForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit designation</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="designationName4Edit">Designation Name</label>
                                <input type="hidden" id="designation_id" name="">
                                <input type="text"  class="form-control validate" data-msg="designation name is required" id="designationName4Edit" name="designationName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                	<option value="Active">Active</option>
                                	<option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit projects -->
<div class="modal  fade"  data-bs-focus="false" id="edit_project" tabindex="-1" role="dialog" aria-labelledby="edit_projectLabel" aria-hidden="true">
    <div class="modal-dialog" role="project" style="width:500px;">
        <form class="modal-content" id="editProjectForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit project </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="projectName4Edit">Project Name</label>
                                <input type="hidden" id="project_id" name="">
                                <input type="text"  class="form-control validate" data-msg="Project name is required" id="projectName4Edit" name="projectName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="comments4Edit">Comments</label>
                                <textarea  class="form-control " id="comments4Edit" name="comments4Edit">
                                </textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                	<option value="Active">Active</option>
                                	<option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit contract types -->
<div class="modal  fade"  data-bs-focus="false" id="edit_contractType" tabindex="-1" role="dialog" aria-labelledby="edit_contractTypeLabel" aria-hidden="true">
    <div class="modal-dialog" role="contractType" style="width:500px;">
        <form class="modal-content" id="editContractTypeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit contract type </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="contractTypeName4Edit">Contract type </label>
                                <input type="hidden" id="contractType_id" name="">
                                <input type="text"  class="form-control validate" data-msg="contractType name is required" id="contractTypeName4Edit" name="contractTypeName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Active">Active</option>
                                    <option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit budget codes -->
<div class="modal  fade"  data-bs-focus="false" id="edit_budgetCode" tabindex="-1" role="dialog" aria-labelledby="edit_budgetCodeLabel" aria-hidden="true">
    <div class="modal-dialog" role="budgetCode" style="width:500px;">
        <form class="modal-content" id="editBudgetCodeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit budget code </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="budgetCode4Edit">Budget code</label>
                                <input type="hidden" id="budget_codeID" name="">
                                <input type="text"  class="form-control validate" data-msg="budgetCode name is required" id="budgetCode4Edit" name="budgetCode4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="comments4Edit">Comments</label>
                                <textarea  class="form-control " id="comments4Edit" name="comments4Edit">
                                </textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Active">Active</option>
                                    <option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit contract types -->
<div class="modal  fade"  data-bs-focus="false" id="edit_bank" tabindex="-1" role="dialog" aria-labelledby="edit_bankLabel" aria-hidden="true">
    <div class="modal-dialog" role="bank" style="width:500px;">
        <form class="modal-content" id="editAllBankForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit bank </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="bankName4Edit">Bank name</label>
                                <input type="hidden" id="bank_id" name="">
                                <input type="text"  class="form-control validate" data-msg="bank name is required" id="bankName4Edit" name="bankName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Active">Active</option>
                                    <option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit contract types -->
<div class="modal  fade"  data-bs-focus="false" id="edit_subtype" tabindex="-1" role="dialog" aria-labelledby="edit_bankLabel" aria-hidden="true">
    <div class="modal-dialog" role="subtype" style="width:500px;">
        <form class="modal-content" id="editSubtype" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit subtype </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="subtypeName4Edit">Subtype name</label>
                                <input type="hidden" id="subtype_id" name="">
                                <input type="text"  class="form-control validate" data-msg="Subtype name is required" id="subtypeName4Edit" name="subtypeName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Active">Active</option>
                                    <option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="transType4Edit"> Type</label>
                                <select  class="form-control validate" data-msg="Please select  type" id="transType4Edit" name="transType4Edit">
                                    <option value="">- Select</option>
                                    <option value="Allowance">Allowance</option>
                                    <option value="Bonus">Bonus</option>
                                    <option value="Commission">Commission</option>
                                    <option value="Advance">Advance</option>
                                    <option value="Deduction">Deduction</option>
                                    <option value="Loan">Loan</option>

                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>