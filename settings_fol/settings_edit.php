<div class="modal  fade"  data-bs-focus="false" id="change_setting" tabindex="-1" role="dialog" aria-labelledby="change_settingLabel" aria-hidden="true">
    <div class="modal-dialog" role="project" style="width:500px;">
        <form class="modal-content changeSettingForm" id="changeSettingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Change setting </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="forSettings">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required d-none" for="settingDetails ">Details</label>
                                <input type="hidden" id="settingType" class="settingType" name="">
                                <input type="hidden" id="settingSection" class="settingSection" name="">
                                <input type="hidden" id="settingRemarks" class="settingRemarks" name="">
                                <input type="text"  class="form-control d-none settingDetails validate" data-msg="Please provide descriptive details" id="settingDetails" name="settingDetails">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="settingValue">Setting</label>
                                <input type="text"  class="form-control settingValue validate" id="settingValue" name="settingValue" data-msg="Setting value is required">
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