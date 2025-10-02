<!-- Disabled Features Modal -->
<div class="modal fade" id="disabledFeaturesModal" tabindex="-1" role="dialog" aria-labelledby="disabledFeaturesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:700px; width: 90vw; max-width: 750px;">
        <form class="modal-content" id="disabledFeaturesForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;" method="post">
            <div class="modal-header">
                <h5 class="modal-title" id="addSystemRole">Disabled Features</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert smt-10 alert-info">
                    <i class="fa fa-info-circle"></i> Uncheck the features you want to disable from the system.
                </div>
                <div id="featuresContainer" class="row">
                   <?php 
                    $sys_features = $GLOBALS['sys_permissions']->read_all();
                    $disabled_features = get_setting('disabled_features');
                    foreach ($sys_features as $feature) {
                        echo '<div class="col-md-6">';
                        echo '<div class="form-check">';
                        echo '<input style="width: 20px; height: 20px; margin-right: 5px;" class="form-check-input" ' . (!in_array($feature['module'], json_decode($disabled_features['value'])) ? 'checked' : '') . ' type="checkbox" value="' . $feature['module'] . '" id="' . $feature['module'] . '">';
                        echo '<label class="form-check-label cursor" for="' . $feature['module'] . '">' . ucwords($feature['module']) . '</label>';
                        echo '</div>';
                        echo '</div>';
                    }
                   ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveDisabledFeatures">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Original Settings Modal -->
<div class="modal fade" data-bs-focus="false" id="change_setting" tabindex="-1" role="dialog" aria-labelledby="change_settingLabel" aria-hidden="true">
    <div class="modal-dialog" role="project" style="width:500px;">
        <form class="modal-content changeSettingForm" id="changeSettingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Change setting</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="forSettings">
                    <div class="row">
                        <div class="col col-xs-12">
                            <label class="label required d-none" for="settingDetails">Details</label>
                            <input type="hidden" id="settingType" class="settingType" name="">
                            <input type="hidden" id="settingSection" class="settingSection" name="">
                            <input type="hidden" id="settingRemarks" class="settingRemarks" name="">
                            <input type="text" class="form-control d-none settingDetails validate" data-msg="Please provide descriptive details" id="settingDetails" name="settingDetails">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="settingValue">Setting</label>
                                <input type="text" class="form-control settingValue validate" id="settingValue" name="settingValue" data-msg="Setting value is required">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>
