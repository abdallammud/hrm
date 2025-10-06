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

<!-- Logo Upload Modal -->
<div class="modal fade" id="uploadLogoModal" tabindex="-1" role="dialog" aria-labelledby="uploadLogoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="project" style="width:500px;">
        <form class="modal-content" id="logoUploadForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;" method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Upload System Logo</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert smt-10 alert-info"><i class="fa fa-info-circle"></i> Recommended PNG or JPG. Max 2MB. Will be stored in <code>assets/images/</code>.</div>
                <input type="hidden" name="type" value="system_logo" class="settingTypeLogo">
                <div class="form-group">
                    <label for="logoFile">Select logo</label>
                    <input type="file" class="form-control" id="logoFile" name="logoFile" accept="image/png,image/jpeg,image/jpg" required>
                    <div class="form-text">Preferably transparent PNG; will be resized by browser constraints when displayed.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" style="min-width:100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width:100px;">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Original Settings Modal (updated to include color input option) -->
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
                            <input type="hidden" id="settingType" class="settingType" name="">
                            <input type="hidden" id="settingSection" class="settingSection" name="">
                            <input type="hidden" id="settingRemarks" class="settingRemarks" name="">
                            <input type="text" class="form-control d-none settingDetails validate" data-msg="Please provide descriptive details" id="settingDetails" name="settingDetails">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>

                    <div class="row color-row d-none">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="settingColor">Choose color</label>
                                <input type="color" class="form-control settingColor" id="settingColor" name="settingColor" style="height:42px;">
                                <small class="form-text text-muted">Selected color will be stored as <code>rgb(r,g,b)</code>.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row value-row">
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

<!-- Email Config Modal -->
<div class="modal fade "  id="emailConfigModal" tabindex="-1" role="dialog" aria-labelledby="emailConfigModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 600px;">
    <form class="modal-content" id="emailConfigForm">
    <div class="modal-header">
        <h5 class="modal-title">Email Configuration</h5>
        <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
    $email_config = get_setting('email_config');
    $email_config = json_decode($email_config['value'], true);
    ?>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label required">SMTP Host</label>
            <input type="text" class="form-control" name="host" value="<?=$email_config['host'];?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label required">SMTP Port</label>
            <input type="number" class="form-control" name="port" value="<?=$email_config['port'];?>" required>
          </div>

          <div class="col-md-6 mt-3">
            <label class="form-label required">Email</label>
            <input type="email" class="form-control" name="username" value="<?=$email_config['username'];?>" required>
          </div>
          <div class="col-md-6 mt-3">
            <label class="form-label required">Password</label>
            <input type="password" class="form-control" name="password" value="<?=$email_config['password'];?>" required>
          </div>

          <div class="col-md-6 mt-3">
            <label class="form-label required">Security</label>
            <select class="form-control" name="secure" required>
              <option value="tls" <?=($email_config['secure'] == 'tls') ? 'selected' : ''?>>TLS</option>
              <option value="ssl" <?=($email_config['secure'] == 'ssl') ? 'selected' : ''?>>SSL</option>
            </select>
          </div>

          <div class="col-md-6 mt-3">
            <label class="form-label required">From Email</label>
            <input type="email" class="form-control" name="from" value="<?=$email_config['from'];?>" required>
          </div>

          <div class="col-md-6 mt-3">
            <label class="form-label required">From Name</label>
            <input type="text" class="form-control" name="fromName" value="<?=$email_config['fromName'];?>" required>
          </div>

          <div class="col-md-6 mt-3">
            <label class="form-label required">Reply-To Email</label>
            <input type="email" class="form-control" name="replyTo" value="<?=$email_config['replyTo'];?>" required>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Settings</button>
      </div>
    </form>
  </div>
</div>
