<div class="modal  fade"   data-bs-focus="false" id="generate_payroll" tabindex="-1" role="dialog" aria-labelledby="generatePayrollLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="width:500px;">
        <form class="modal-content" id="generatePayrollForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add payroll</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcTransFor">Payroll for</label>
                                <select type="text"  class="form-control validate slcTransFor" data-msg="Please select transaction for" name="slcTransFor" id="slcTransFor">
                                    <option value="All"> All</option>
                                    <option value="Department"> Department</option>
                                    <option value="Location"> Duty Location</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group  attenForDiv">
                                
                            </div>
                        </div>
                    </div>
                   
                    <div class="row">
                        <div class="col col-xs-12">
                            <label class="label required" for="slcYear">Payroll Period</label>
                            <div class="form-group sflex">
                                <select type="text"  class="form-control sflex-basis-26 smr-15  slcYear" name="slcYear" id="slcYear">
                                    <?php
                                        for ($year = 2025; $year >= 2015; $year--) { 
                                          echo "<option value=\"{$year}\">{$year}</option>";
                                        }
                                    ?>
                                </select>
                                <select name="" class="SlectBox" multiple="multiple" id="slctedMonths">
                                    <?php 
                                    $months = array('01' => 'January','02' => 'February','03' => 'March','04' => 'April','05' => 'May','06' => 'June','07' => 'July','08' => 'August','09' => 'September','10' => 'October','11' => 'November','12' => 'December');
                                    $currentYear = date('Y');
                                    foreach ($months as $monthNum => $monthName) {
                                        $selected = $monthNum == date("m") ? 'selected' : ''; // Adjust array as needed
                                        echo "<option value=\"{$currentYear}-{$monthNum}\" {$selected}>{$monthName} {$currentYear}</option>";
                                    }
                                    ?>
                                </select>
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

<style type="text/css">
    .SumoSelect > .CaptionCont {
        background: var(--bs-body-bg-2);;
    }
    .main-wrapper .main-content .options {
        display: flex;
        align-items: center;
        color: #494949;
        border-radius: 0%; 
        transition: all 0.3s;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        height: fit-content;
        background: var(--bs-body-bg-2);;
    }

    .main-wrapper .main-content .options li {
        flex-basis: 100%;
    }

    .main-wrapper .main-content .options li.opt {
        border-bottom: var(--bs-border-width) solid var(--bs-border-color);
    }
    .main-wrapper .main-content .options li.opt:hover {
        background: var(--bs-body-bg-2);
        opacity: .7;
    }

    .SumoSelect {
        /* width: 98%; */
        flex-basis: 70%;
        border-radius: 5px;
    }
    .SumoSelect > .CaptionCont {
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: 5px;
    } 

    .SumoSelect > .optWrapper.multiple > .options li.opt.selected span i, .SumoSelect .select-all.selected > span i, .SumoSelect .select-all.partial > span i {
        background-color: #2e80f9;
    }
</style>