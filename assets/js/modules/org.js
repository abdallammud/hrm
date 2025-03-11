function load_comapny() {
	var datatable = $('#companyDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [4] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=company",
	        "method": "POST",
		    // dataFilter: function(data) {
			// 	console.log(data)
			// }
	    },
	    
	    "drawCallback": function(settings) {
	        
	    },
	    columns: [
	        { title: "Organization Name", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Phone Numbers", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.contact_phone}</span>
	                </div>`;
	        }},

	        { title: "Emails", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.contact_email}</span>
	                </div>`;
	        }},

	        { title: "Address", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.address}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
	            		<span data-recid="${row.id}" class="fa edit_companyInfo smt-5 cursor smr-10 fa-pencil"></span>
	            		<span data-recid="${row.id}" class="fa delete_company smt-5 cursor fa-trash"></span>
	                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleOrg() {
	$('#addOrgForm').on('submit', (e) => {
		handle_addCompanyForm(e.target);
		return false
	})

	load_comapny();

	// edit company info popup
	$(document).on('click', '.edit_companyInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_org');

	    let data = await get_company(id)
	    if(data) {
	    	let res = JSON.parse(data)[0]

	    	$(modal).find('#company_id').val(id)
	    	$(modal).find('#orgName4Edit').val(res.name)
	    	$(modal).find('#contactPhone4Edit').val(res.contact_phone) 
	    	$(modal).find('#contactEmail4Edit').val(res.contact_email)
	    	$(modal).find('#txtAddress4Edit').val(res.address)
	    }

	    $(modal).modal('show');
	});

	$('#editOrgForm').on('submit', (e) => {
		handle_editCompanyForm(e.target);
		return false
	})

	$(document).on('click', '.delete_company', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: "You are going to delete this company record.",
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete company', data);

	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 2000 }).then(() => {
	                            // location.reload();
	                            load_comapny();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit company.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function send_orgPost(str, data) {
    let [action, endpoint] = str.split(' ');

    try {
        const response = await $.post(`./app/org_controller.php?action=${action}&endpoint=${endpoint}`, data);
        return response;
    } catch (error) {
        console.error('Error occurred during the request:', error);
        return null;
    }
}

async function handle_addCompanyForm(form) {
    clearErrors();

    let name = $(form).find('#orgName').val();
    let phones = $(form).find('#contactPhone').val();
    let emails = $(form).find('#contactEmail').val();
    let address = $(form).find('#txtAddress').val();

    // Input validation
    let error = false;
    error = !validateField(name, "Company name is required", 'orgName') || error;
    error = !validateField(phones, "Company phone number is required", 'contactPhone') || error;

    if (error) return false;

    let formData = {
        name: name,
        phones: phones,
        emails: emails,
        address: address
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save company', formData);

        if (response) {
            let res = JSON.parse(response)
            $('#add_org').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            		location.reload();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save company.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editCompanyForm(form) {
    clearErrors();

    let id = $(form).find('#company_id').val();
    let name = $(form).find('#orgName4Edit').val();
    let phones = $(form).find('#contactPhone4Edit').val();
    let emails = $(form).find('#contactEmail4Edit').val();
    let address = $(form).find('#txtAddress4Edit').val();

    // Input validation
    let error = false;
    error = !validateField(name, "Company name is required", 'orgName4Edit') || error;
    error = !validateField(phones, "Company phone number is required", 'contactPhone4Edit') || error;

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        phones: phones,
        emails: emails,
        address: address
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update company', formData);

        if (response) {
            let res = JSON.parse(response)
            $('#edit_org').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            		location.reload();
            		// load_comapny();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to edit company.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_company(id) {
	let data = {id};
	let response = await send_orgPost('get company', data);
	return response;
}


// Branches
function load_branches() {
	var datatable = $('#branchesDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=branches",
	        "method": "POST",
		    // dataFilter: function(data) {
			// 	console.log(data)
			// }
	    },
	    
	    "drawCallback": function(settings) {
	        
	    },
	    columns: [
	        { title: `${branch_keyword.sing} Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	       /* { title: "Phone Numbers", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.contact_phone}</span>
	                </div>`;
	        }},

	        { title: "Emails", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.contact_email}</span>
	                </div>`;
	        }},

	        { title: "Address", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.address}</span>
	                </div>`;
	        }},*/

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
	            		<span data-recid="${row.id}" class="fa edit_branchInfo smt-5 cursor smr-10 fa-pencil"></span>
	            		<span data-recid="${row.id}" class="fa delete_branch smt-5 cursor fa-trash"></span>
	                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleBranches() {
	$('#addBranchForm').on('submit', (e) => {
		handle_addBranchForm(e.target);
		return false
	})

	load_branches();

	$(document).on('click', '.edit_branchInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_branch');

	    let data = await get_branch(id)
	    if(data) {
	    	let res = JSON.parse(data)[0]

	    	$(modal).find('#branch_id').val(id)
	    	$(modal).find('#branchName4Edit').val(res.name)
	    	$(modal).find('#contactPhone4Edit').val(res.contact_phone) 
	    	$(modal).find('#contactEmail4Edit').val(res.contact_email)
	    	$(modal).find('#txtAddress4Edit').val(res.address)
	    }

	    $(modal).modal('show');
	});

	$('#editBranchForm').on('submit', (e) => {
		handle_editBranchForm(e.target);
		return false
	})

	$(document).on('click', '.delete_branch', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this ${branch_keyword.sing} record.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete branch', data);

	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 2000 }).then(() => {
	                            // location.reload();
	                            load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit branch.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addBranchForm(form) {
    clearErrors();

    let name = $(form).find('#branchName').val();
    /*let phones = $(form).find('#contactPhone').val();
    let emails = $(form).find('#contactEmail').val();
    let address = $(form).find('#txtAddress').val();*/

    // Input validation
    let error = false;
    /*error = !validateField(name, `${branch_keyword.sing} name is required`, 'branchName') || error;
    error = !validateField(phones, `${branch_keyword.sing} phone number is required`, 'contactPhone') || error;*/

    if (error) return false;

    let formData = {
        name: name
        /*phones: phones,
        emails: emails,
        address: address*/
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save branch', formData);

        if (response) {
            let res = JSON.parse(response)
            $('#add_branch').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            		location.reload();
            		// load_branches();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save branch.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editBranchForm(form) {
    clearErrors();

    let id = $(form).find('#branch_id').val();
    let name = $(form).find('#branchName4Edit').val();
    /*let phones = $(form).find('#contactPhone4Edit').val();
    let emails = $(form).find('#contactEmail4Edit').val();
    let address = $(form).find('#txtAddress4Edit').val();*/

    // Input validation
    let error = false;
    /*error = !validateField(name, `${branch_keyword.sing} name is required`, 'branchName4Edit') || error;
    error = !validateField(phones, `${branch_keyword.sing} phone number is required`, 'contactPhone4Edit') || error;*/

    if (error) return false;

    let formData = {
    	id:id,
        name: name
        /*phones: phones,
        emails: emails,
        address: address*/
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update branch', formData);

        if (response) {
            let res = JSON.parse(response)
            $('#edit_branch').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            		location.reload();
            		// load_branches();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to edit company.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_branch(id) {
	let data = {id};
	let response = await send_orgPost('get branch', data);
	return response;
}

// States
function load_states() {
	var datatable = $('#statesDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=states",
	        "method": "POST",
		    // dataFilter: function(data) {
			// 	console.log(data)
			// }
	    },
	    
	    "drawCallback": function(settings) {
	        
	    },
	    columns: [
	        { title: `State Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa show_stateDetails smt-5 cursor smr-10 fa-eye"></span>
            		<span data-recid="${row.id}" class="fa edit_stateInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_state smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleStates() {
	// States
	$(document).on('click', '.add-tax-grid-row', function(e) {
	    let prevRow = $(e.target).siblings(".row.tax-grid-row").last();
	    if(prevRow.length == 0) {
	    	prevRow = $(e.target).siblings(".tax-gridRows").find('.row.tax-grid-row').last();
	    }
	    console.log($(e.target).siblings(".row.tax-grid-row"))

	    // return false;
	    let newRow = `<div class="row tax-grid-row" style="margin-top: 5px;">
	            <div class="col-sm-4">
	            	<input type="text" onkeypress="return isNumberKey(event)"  class="form-control min-amount col-sm-4 col-lg-4">
	            </div>
	            <div class="col-sm-4">
	            	<input type="text" onkeypress="return isNumberKey(event)"  class="form-control max-amount col-sm-4 col-lg-4 validate">
	            </div>
	            <div class="col-sm-3">
	            	<input type="text" onkeypress="return isNumberKey(event)"  class="form-control rate col-sm-4 col-lg-4 validate">
	            </div>
	            <div class="col-sm-1">
	            	<i class="fa fa-trash-alt remove-tax-grid-row cursor mt-2"></i>
	            </div>
	        </div>`;

	    // Insert the new row after the current row
	    $(prevRow).after(newRow);
	});

	$(document).on('click', '.remove-tax-grid-row', function(e) {
	    e.preventDefault();
	    let prevRow = $(e.target).closest('.row');
	    $(prevRow).fadeOut(500, function() {
	        $(this).remove();
	    });
	});

	load_states();

	// Add state
	$('#addStateForm').on('submit', (e) => {
		handle_addStateForm(e.target);
		return false
	})

	// Show state
	$(document).on('click', '.show_stateDetails', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#show_state');

	    let data = await get_state(id, true)
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data);
	    	$(modal).find('#detailsTable tbody').html(res.details)
	    	$(modal).find('#tax-grid tbody').html(res.tax)
	    }
	    // return false;
	   
	    $(modal).modal('show');
	});

	// Edit state
	$(document).on('click', '.edit_stateInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_state');

	    let data = await get_state(id, false);
	    // console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	if(res.tax_grid) {
		    	let tax = JSON.parse(res.tax_grid);
		    	let tax_gridRows = '';
		    	if(tax.length > 0) {
		    		tax_gridRows += `<div class="row tax-grid-row" style="margin-top: 2px;">
	                    <div class="col-sm-4">
	                    	<label class="label required">Min amount</label>
	                    	<input type="text" value="${tax[0].min}" onkeypress="return isNumberKey(event)" class="form-control min-amount col-sm-4 col-lg-4">
	                    </div>
	                    <div class="col-sm-4">
	                    	<label class="label required">Max amount</label>
	                    	<input type="text" value="${tax[0].max}" onkeypress="return isNumberKey(event)" class="form-control max-amount col-sm-4 col-lg-4">
	                    </div>
	                    <div class="col-sm-3">
	                    	<label class="label required">Rate</label>
	                    	<input type="text" value="${tax[0].rate}" onkeypress="return isNumberKey(event)" class="form-control rate col-sm-4 col-lg-4">
	                    	
	                    </div>
	                    <div class="col-sm-1">
	                    	<label class="label required">&nbsp;</label>
	                    	<i class="fa fa-trash-alt remove-tax-grid-row cursor mt-2"></i>
	                    </div>
	                </div>`
		    	}

		    	for (var i = 1; i < tax.length; i++) {
		    		tax_gridRows += `<div class="row tax-grid-row" style="margin-top: 5px;">
			            <div class="col-sm-4">
			            	<input type="text" value="${tax[i].min}" onkeypress="return isNumberKey(event)"  class="form-control min-amount col-sm-4 col-lg-4">
			            </div>
			            <div class="col-sm-4">
			            	<input type="text" value="${tax[i].max}" onkeypress="return isNumberKey(event)"  class="form-control max-amount col-sm-4 col-lg-4 validate">
			            </div>
			            <div class="col-sm-3">
			            	<input type="text" value="${tax[i].rate}" onkeypress="return isNumberKey(event)"  class="form-control rate col-sm-4 col-lg-4 validate">
			            </div>
			            <div class="col-sm-1">
			            	<i class="fa fa-trash-alt remove-tax-grid-row cursor mt-2"></i>
			            </div>
			        </div>`
		    	}

		    	$('.tax-gridRows').html(tax_gridRows)
		    }

	    	$(modal).find('#state_id').val(id);
	    	$(modal).find('#stateName').val(res.name);
	    	$(modal).find('#stampDuty').val(res.stamp_duty);
	    	$(modal).find('#stateCountry').val(res.country_id) ;
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit state info form
	$('#editStateForm').on('submit', (e) => {
		handle_editStateForm(e.target);
		return false
	})

	// Delete state
	$(document).on('click', '.delete_state', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this state record.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete state', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addStateForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#stateName').val();
    let country = $(form).find('#stateCountry').val();
    let countryName = $(form).find('#stateCountry option:selected').text();
    let tax 	= [];
    let stampDuty 	= $(form).find('#stampDuty').val();

    $('.tax-grid-row').each((i, el) => {
	    let min = parseFloat($(el).find('.min-amount').val()) || 0; 
	    let max = parseFloat($(el).find('.max-amount').val());
	    let rate = parseFloat($(el).find('.rate').val());

	    // Only add valid rows to the tax array
	    if (!isNaN(max) && max > min) {
	        let obj = { "min": min, "max": max, "rate": rate || 0 }; 
	        tax.push(obj);
	    }
	});

    if (error) return false;

    let formData = {
        name: name,
        country: country,
        tax: tax,
        countryName:countryName,
        stampDuty:stampDuty
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save state', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editStateForm(form) {
    clearErrors();
    let error = validateForm(form)

    let id 		= $(form).find('#state_id').val();
    let name 	= $(form).find('#stateName').val();
    let country = $(form).find('#stateCountry').val();
    let countryName = $(form).find('#stateCountry option:selected').text();
    let tax 		= [];
    let stampDuty 	= $(form).find('#stampDuty').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    $('.tax-grid-row').each((i, el) => {
	    let min = parseFloat($(el).find('.min-amount').val()) || 0; 
	    let max = parseFloat($(el).find('.max-amount').val());
	    let rate = parseFloat($(el).find('.rate').val());

	    // Only add valid rows to the tax array
	    if (!isNaN(max) && max > min) {
	        let obj = { "min": min, "max": max, "rate": rate || 0 }; 
	        tax.push(obj);
	    }
	});

    if (error) return false;

    let formData = {
    	id: id,
        name: name,
        status: slcStatus,
        country: country,
        tax: tax,
        countryName:countryName,
        stampDuty:stampDuty
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update state', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_state(id, show = false) {
	let data = {id, show};
	let response = await send_orgPost('get state', data);
	return response;
}


// Locations
function load_locations() {
	var datatable = $('#locationsDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [3] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=locations",
	        "method": "POST",
		    // dataFilter: function(data) {
			// 	console.log(data)
			// }
	    },
	    
	    "drawCallback": function(settings) {
	        
	    },
	    columns: [
	        { title: `Duty Location`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: `City`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.city_name}</span>
	                </div>`;
	        }},

	        { title: `State`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.state_name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_locationInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_location smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleLocations() {
	$('#addLocationForm').on('submit', (e) => {
		handle_addLocationForm(e.target);
		return false
	})

	load_locations();

	// Edit location
	$(document).on('click', '.edit_locationInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_location');

	    let data = await get_location(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#location_id').val(id);
	    	$(modal).find('#locationName4Edit').val(res.name);
	    	$(modal).find('#city4Edit').val(res.city_name);
	    	$(modal).find('#state4Edit').val(res.state_id) ;
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editLocationForm').on('submit', (e) => {
		handle_editLocationForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_location', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this duty location record.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete location', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addLocationForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#locationName').val();
    let city 	= $(form).find('#city').val();
    let state 	= $(form).find('#state').val();
    let stateName = $(form).find('#state option:selected').text();

    if (error) return false;

    let formData = {
        name: name,
        city: city,
        state: state,
        stateName:stateName
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save location', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editLocationForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 		= $(form).find('#location_id').val();
    let name 	= $(form).find('#locationName4Edit').val();
    let city 	= $(form).find('#city4Edit').val();
    let state 	= $(form).find('#state4Edit').val();
    let stateName = $(form).find('#state4Edit option:selected').text();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        city: city,
        state: state,
        stateName:stateName,
        slcStatus:slcStatus
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update location', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_location(id) {
	let data = {id};
	let response = await send_orgPost('get location', data);
	return response;
}


// Banks
function load_banks() {
	var datatable = $('#banksDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [4] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=bank_accounts",
	        "method": "POST",
		    // dataFilter: function(data) {
			// 	console.log(data)
			// }
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Bank name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.bank_name}</span>
	                </div>`;
	        }},

	        { title: `Account number`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.account}</span>
	                </div>`;
	        }},

	        { title: `Current balance`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${formatMoney(row.balance)}</span>
	                </div>`;
	        }},

	        { title: `Status`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.status}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_bankInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_bank smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleBanks() {
	$('#addBankForm').on('submit', (e) => {
		handle_addBankForm(e.target);
		return false
	})

	load_banks();

	// Edit location
	$(document).on('click', '.edit_bankInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_bank');

	    let data = await get_bank_account(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#bank_account_id').val(id);
	    	$(modal).find('#bankName4Edit').val(res.bank_name);
	    	$(modal).find('#account4Edit').val(res.account);
	    	$(modal).find('#balance4Edit').val(res.balance) ;
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editBankForm').on('submit', (e) => {
		handle_editBankForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_bank', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this bank account.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete bank_account', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
	(function() {
		// console.log(branch_keyword)
	})();
}

async function handle_addBankForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#bankName').val();
    let account 	= $(form).find('#account').val();
    let balance 	= $(form).find('#balance').val();

    if (error) return false;

    let formData = {
        name: name,
        account: account,
        balance: balance,
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save bank_account', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_bank').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_banks();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editBankForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#bank_account_id').val();
   	let name 	= $(form).find('#bankName4Edit').val();
    let account 	= $(form).find('#account4Edit').val();
    let balance 	= $(form).find('#balance4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        account: account,
        balance: balance,
        slcStatus:slcStatus
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update bank_account', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_bank_account(id) {
	let data = {id};
	let response = await send_orgPost('get bank_account', data);
	return response;
}

// Designation
function load_designations() {
	var datatable = $('#designationsDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=designations",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_designationInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_designation smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleDesignations() {
	$('#addDesignationForm').on('submit', (e) => {
		handle_addDesignationForm(e.target);
		return false
	})

	load_designations();

	// Edit location
	$(document).on('click', '.edit_designationInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_designation');

	    let data = await get_designation(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#designation_id').val(id);
	    	$(modal).find('#designationName4Edit').val(res.name);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editDesignationForm').on('submit', (e) => {
		handle_editDesignationForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_designation', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this designation.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete designation', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addDesignationForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#designationName').val();

    if (error) return false;

    let formData = {
        name: name
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save designation', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_designation').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_designations();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editDesignationForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#designation_id').val();
   	let name 	= $(form).find('#designationName4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        slcStatus:slcStatus
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update designation', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_designation(id) {
	let data = {id};
	let response = await send_orgPost('get designation', data);
	return response;
}

// Projects
function load_projects() {
	var datatable = $('#projectsDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false,  "targets": [2] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=projects",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: `Comments`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.comments}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_projectInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_project smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleProjects() {
	$('#addProjectForm').on('submit', (e) => {
		handle_addProjectForm(e.target);
		return false
	})

	load_projects();

	// Edit location
	$(document).on('click', '.edit_projectInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_project');

	    let data = await get_project(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#project_id').val(id);
	    	$(modal).find('#projectName4Edit').val(res.name);
	    	$(modal).find('#comments4Edit').val(res.comments);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editProjectForm').on('submit', (e) => {
		handle_editProjectForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_project', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this project.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete project', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addProjectForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#projectName').val();
    let comments = $(form).find('#comments').val();

    if (error) return false;

    let formData = {
        name: name,
        comments:comments
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save project', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_project').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_projects();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editProjectForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#project_id').val();
   	let name 	= $(form).find('#projectName4Edit').val();
   	let comments 	= $(form).find('#comments4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        comments: comments,
        slcStatus:slcStatus
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update project', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_project(id) {
	let data = {id};
	let response = await send_orgPost('get project', data);
	return response;
}


// Contract types
function load_contractTypes() {
	var datatable = $('#contractTypesDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false,  "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=contract_types",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Contract Type`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_contractTypeInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_contractType smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleContractTypes() {
	$('#add_contractType').on('submit', (e) => {
		handle_addContractTypeForm(e.target);
		return false
	})

	load_contractTypes();

	// Edit location
	$(document).on('click', '.edit_contractTypeInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_contractType');

	    let data = await get_contractType(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#contractType_id').val(id);
	    	$(modal).find('#contractTypeName4Edit').val(res.name);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editContractTypeForm').on('submit', (e) => {
		handle_editContractTypeForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_contractType', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this contract type.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete contract_type', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addContractTypeForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#contractTypeName').val();

    if (error) return false;

    let formData = {
        name: name,
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save contract_type', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_contractType').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_contractTypes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editContractTypeForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#contractType_id').val();
   	let name 	= $(form).find('#contractTypeName4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        slcStatus:slcStatus
    };

    form_loading(form);

    try {
        let response = await send_orgPost('update contract_type', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_state').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_states();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_contractType(id) {
	let data = {id};
	let response = await send_orgPost('get contract_type', data);
	return response;
}


// Budget codes
function load_budgetCodes() {
	var datatable = $('#budgetCodesDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false,  "targets": [2] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=budget_codes",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: `Comments`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.comments}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_budgetCodeInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_budgetCode smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleBudgetCodes() {
	$('#addBudgetCodeForm').on('submit', (e) => {
		handle_addBudgetCodeForm(e.target);
		return false
	})

	load_budgetCodes();

	// Edit location
	$(document).on('click', '.edit_budgetCodeInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_budgetCode');

	    let data = await get_budgetCode(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#budget_codeID').val(id);
	    	$(modal).find('#budgetCode4Edit').val(res.name);
	    	$(modal).find('#comments4Edit').val(res.comments);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editBudgetCodeForm').on('submit', (e) => {
		handle_editBudgetCodeForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_budgetCode', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this budget code.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete budget_code', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addBudgetCodeForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#budgetCode').val();
    let comments = $(form).find('#comments').val();

    if (error) return false;

    let formData = {
        name: name,
        comments:comments
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save budget_code', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_budgetCode').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editBudgetCodeForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#budget_codeID').val();
   	let name 	= $(form).find('#budgetCode4Edit').val();
   	let comments 	= $(form).find('#comments4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        comments: comments,
        slcStatus:slcStatus
    };

    form_loading(form);
    try {
        let response = await send_orgPost('update budget_code', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#edit_budgetCode').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_budgetCode(id) {
	let data = {id};
	let response = await send_orgPost('get budget_code', data);
	return response;
}

// Budget all banks
function load_allBanks() {
	var datatable = $('#allBanksDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false,  "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=all_banks",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_allBanksInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_allBanks smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleAllBanks() {
	$('#addbankForm').on('submit', (e) => {
		handle_addbankForm(e.target);
		return false
	})

	load_allBanks();

	// Edit location
	$(document).on('click', '.edit_allBanksInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_bank');

	    let data = await get_bank(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#bank_id').val(id);
	    	$(modal).find('#bankName4Edit').val(res.name);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editAllBankForm').on('submit', (e) => {
		handle_editAllBankForm(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_allBanks', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this bank.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete bank', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addbankForm(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#bankName').val();

    if (error) return false;

    let formData = {
        name: name,
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save bank', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_budgetCode').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editAllBankForm(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#bank_id').val();
   	let name 	= $(form).find('#bankName4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        slcStatus:slcStatus
    };

    form_loading(form);
    try {
        let response = await send_orgPost('update bank', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#edit_budgetCode').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_bank(id) {
	let data = {id};
	let response = await send_orgPost('get bank', data);
	return response;
}


// Budget subtypes
function load_transSubTypes() {
	var datatable = $('#subTypesDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "searching": false,  
	    "info": false,
	    "columnDefs": [
	        { "orderable": false, "searchable": false,  "targets": [1] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/org_controller.php?action=load&endpoint=subtypes",
	        "method": "POST",
		    /*dataFilter: function(data) {
				console.log(data)
			}*/
	    },
	    
	    "createdRow": function(row, data, dataIndex) { 
	    	// Add your custom class to the row 
	    	$(row).addClass('table-row ' +data.status.toLowerCase());
	    },
	    columns: [
	        { title: `Type`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.type}</span>
	                </div>`;
	        }},

	        { title: `Name`, data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.name}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
            		<span data-recid="${row.id}" class="fa edit_subTypesInfo smt-5 cursor smr-10 fa-pencil"></span>
            		<span data-recid="${row.id}" class="fa delete_subType smt-5 cursor fa-trash"></span>
                </div>`;
	        }},
	    ]
	});

	return false;
}

function handleSubTypes() {
	$('#addSubtype').on('submit', (e) => {
		handle_addSubtype(e.target);
		return false
	})

	load_transSubTypes();

	// Edit location
	$(document).on('click', '.edit_subTypesInfo', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    let modal = $('#edit_subtype');

	    let data = await get_subtype(id);
	    console.log(data)
	    if(data) {
	    	let res = JSON.parse(data)[0];
	    	console.log(res)
	    	$(modal).find('#subtype_id').val(id);
	    	$(modal).find('#subtypeName4Edit').val(res.name);
	    	$(modal).find('#transType4Edit').val(res.type);
	    	$(modal).find('#slcStatus').val(res.status);
	    }

	    $(modal).modal('show');
	});

	// Edit location info form
	$('#editSubtype').on('submit', (e) => {
		handle_editSubtype(e.target);
		return false
	})

	// Delete location
	$(document).on('click', '.delete_subType', async (e) => {
	    let id = $(e.currentTarget).data('recid');
	    swal({
	        title: "Are you sure?",
	        text: `You are going to delete this record.`,
	        icon: "warning",
	        className: 'warning-swal',
	        buttons: ["Cancel", "Yes, delete"],
	    }).then(async (confirm) => {
	        if (confirm) {
	            let data = { id: id };
	            try {
	                let response = await send_orgPost('delete subtype', data);
	                console.log(response)
	                if (response) {
	                    let res = JSON.parse(response);
	                    if (res.error) {
	                        toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
	                    } else {
	                        toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
	                            location.reload();
	                            // load_branches();
	                        });
	                        console.log(res);
	                    }
	                } else {
	                    console.log('Failed to edit state.' + response);
	                }

	            } catch (err) {
	                console.error('Error occurred during form submission:', err);
	            }
	        }
	    });
	});
}

async function handle_addSubtype(form) {
    clearErrors();
    let error = validateForm(form)

    let name 	= $(form).find('#subtypeName').val();
    let type 	= $(form).find('#transType').val();

    if (error) return false;

    let formData = {
        name: name,
        type:type
    };

    form_loading(form);

    try {
        let response = await send_orgPost('save subtype', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function handle_editSubtype(form) {
    clearErrors();
    let error = validateForm(form)

    console.log(form)

    let id 	= $(form).find('#subtype_id').val();
   	let name 	= $(form).find('#subtypeName4Edit').val();
   	let type 	= $(form).find('#transType4Edit').val();
    let slcStatus 	= $(form).find('#slcStatus').val();

    if (error) return false;

    let formData = {
    	id:id,
        name: name,
        type:type,
        slcStatus:slcStatus
    };

    form_loading(form);
    try {
        let response = await send_orgPost('update subtype', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#edit_budgetCode').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            		// load_budgetCodes();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

async function get_subtype(id) {
	let data = {id};
	let response = await send_orgPost('get subtype', data);
	return response;
}


document.addEventListener("DOMContentLoaded", function() {
	handleOrg();
	handleBranches();
	handleStates();
	handleLocations();
	handleBanks();
	handleDesignations();
	handleProjects();
	handleContractTypes();
	handleBudgetCodes();
	handleAllBanks();
	handleSubTypes();
});