async function send_performancePost(str, data) {
    let [action, endpoint] = str.split(' ');

    try {
        const response = await $.post(`${base_url}/app/performance_controller.php?action=${action}&endpoint=${endpoint}`, data);
        return response;
    } catch (error) {
        console.error('Error occurred during the request:', error);
        return null;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    load_indicators();
    // Initialize indicators functionality
    $('#addIndicatorsForm').on('submit', (e) => {
        handle_addIndicatorsForm(e.target);
        return false;
    });
    
    $('#editIndicatorsForm').on('submit', (e) => {
        handle_editIndicatorsForm(e.target);
        return false;
    });
    
    // Edit indicator
    $(document).on('click', '.edit_indicator', function(e) {
        let id = $(e.currentTarget).data('recid');
        get_indicator(id);
    });
    
    // Delete indicator
    $(document).on('click', '.delete_indicator', async (e) => {
        let id = $(e.currentTarget).data('recid');
        swal({
            title: "Are you sure?",
            text: `You are going to delete this indicator.`,
            icon: "warning",
            className: 'warning-swal',
            buttons: ["Cancel", "Yes, delete"],
        }).then(async (confirm) => {
            if (confirm) {
                let data = { id: id };
                try {
                    let response = await send_performancePost('delete indicator', data);
                    if (response) {
                        let res = JSON.parse(response);
                        if (res.error) {
                            toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
                        } else {
                            toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
                                load_indicators();
                            });
                        }
                    } else {
                        console.log('Failed to delete indicator.' + response);
                    }
                } catch (err) {
                    console.error('Error occurred during delete:', err);
                }
            }
        });
    });

    // Appraisals
    handleAppraisals();
    $('.my-select').selectpicker({
	    noneResultsText: "No results found"
	});

    // Search employee
    $(document).on('keyup', '.bootstrap-select.searchEmployee input.form-control', async (e) => {
        let search = $(e.target).val();
        let searchFor = 'appraisal';
        let formData = {search:search, searchFor:searchFor}
        if(search) {
            try { 
                let response = await send_performancePost('search employee4Select', formData);
                let res = JSON.parse(response);
                if(!res.error) {
                    $('#searchEmployee').html(res.options)
                    $('.my-select').selectpicker('refresh');
                } 
            } catch (err) {
                console.error('Error occurred during employee search:', err);
            }
        }
    });
});

// Load indicators into DataTable
function load_indicators() {
    if ($.fn.DataTable.isDataTable('#indicatorsDT')) {
        $('#indicatorsDT').DataTable().destroy();
    }
    
    $('#indicatorsDT').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `${base_url}/app/performance_controller.php?action=load&endpoint=indicators`,
            type: "POST"
        },
        columns: [
            { data: "department" },
            { data: "designation" },
            { 
                data: "overall_rating",
                render: function(data, type, row) {
                    if (type === 'display') {
                        let stars = '';
                        const rating = parseFloat(data);
                        const fullStars = Math.floor(rating);
                        const hasHalfStar = rating % 1 >= 0.5;
                        
                        for (let i = 0; i < fullStars; i++) {
                            stars += '<i class="bi bi-star-fill text-warning"></i>';
                        }
                        
                        if (hasHalfStar) {
                            stars += '<i class="bi bi-star-half text-warning"></i>';
                        }
                        
                        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
                        for (let i = 0; i < emptyStars; i++) {
                            stars += '<i class="bi bi-star text-warning"></i>';
                        }
                        
                        return `<div>${stars} <span class="ms-1">${rating}</span></div>`;
                    }
                    return data;
                }
            },
            { data: "added_date" },
            { data: "actions", orderable: false }
        ],
        order: [[0, 'asc']]
    });
}

// Handle add indicator form submission
async function handle_addIndicatorsForm(form) {
    let error = validateForm(form)
    let department_id = $('#slcDepartment').val();
    let designation_id = $('#slcDesignation').val();
    let department = $('#slcDepartment option:selected').text();
    let designation = $('#slcDesignation option:selected').text();
    
    if (error) return false;
    // Get form data
    let formData = {
        department_id: department_id,
        designation_id: designation_id,
        department: department,
        designation: designation,
        business_pro: $('input[name="business_pro"]:checked').val() || 0,
        oral_com: $('input[name="oral_com"]:checked').val() || 0,
        leadership: $('input[name="leadership"]:checked').val() || 0,
        project_mgt: $('input[name="project_mgt"]:checked').val() || 0,
        res_allocating: $('input[name="res_allocating"]:checked').val() || 0
    };

    console.log(formData);
    
    try {
        let response = await send_performancePost('save indicators', formData);
        // console.log(response);
        if (response) {
            let res = JSON.parse(response);
            if (res.error) {
                toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
                toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
                    // Reset form
                    $('#addIndicatorsForm')[0].reset();
                    $('#add_indicators').modal('hide');
                    load_indicators();
                });
            }
        } else {
            console.log('Failed to add indicator.');
        }
    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

// Get indicator details for editing
async function get_indicator(id) {
    let data = { id: id };
    try {
        let response = await send_performancePost('get indicator', data);
        if (response) {
            let res = JSON.parse(response);
            if (res.error) {
                toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
                let indicator = res.data;
                let attributes = JSON.parse(indicator.attributes);
                
                if(indicator.department_id == 0) indicator.department_id = "All";
                if(indicator.designation_id == 0) indicator.designation_id = "All";
                
                // Set form values
                $('#edit_indicator_id').val(indicator.id);
                $('#edit_slcDepartment').val(indicator.department_id);
                $('#edit_slcDesignation').val(indicator.designation_id);
                
                // Set ratings for each competency
                // Behavioural Competencies
                let businessProcess = attributes['Behavioural Competencies'].find(item => item.name === 'Business Process');
                if (businessProcess && businessProcess.rating > 0) {
                    $(`#edit_business_pro_${businessProcess.rating}`).prop('checked', true);
                }
                
                let oralCommunication = attributes['Behavioural Competencies'].find(item => item.name === 'Oral Communication');
                if (oralCommunication && oralCommunication.rating > 0) {
                    $(`#edit_oral_com_${oralCommunication.rating}`).prop('checked', true);
                }
                
                // Organizational Competencies
                let leadership = attributes['Organizational Competencies'].find(item => item.name === 'Leadership');
                if (leadership && leadership.rating > 0) {
                    $(`#edit_leadership_${leadership.rating}`).prop('checked', true);
                }
                
                let projectManagement = attributes['Organizational Competencies'].find(item => item.name === 'Project Management');
                if (projectManagement && projectManagement.rating > 0) {
                    $(`#edit_project_mgt_${projectManagement.rating}`).prop('checked', true);
                }
                
                // Technical Competencies
                let allocatingResources = attributes['Technical Competencies'].find(item => item.name === 'Allocating Resources');
                if (allocatingResources && allocatingResources.rating > 0) {
                    $(`#edit_res_allocating_${allocatingResources.rating}`).prop('checked', true);
                }
                
                // Show edit modal
                $('#edit_indicators').modal('show');
            }
        } else {
            console.log('Failed to get indicator details.');
        }
    } catch (err) {
        console.error('Error occurred while getting indicator details:', err);
    }
}

// Handle edit indicator form submission
async function handle_editIndicatorsForm(form) {
    let error = validateForm(form)
    if (error) return false;
    
    let id = $('#edit_indicator_id').val();
    let department_id = $('#edit_slcDepartment').val();
    let designation_id = $('#edit_slcDesignation').val();
    let department = $('#edit_slcDepartment option:selected').text();
    let designation = $('#edit_slcDesignation option:selected').text();
    
    // Get form data
    let formData = {
        id: id,
        department_id: department_id,
        designation_id: designation_id,
        department: department,
        designation: designation,
        business_pro: $('input[name="business_pro"]:checked').val() || 0,
        oral_com: $('input[name="oral_com"]:checked').val() || 0,
        leadership: $('input[name="leadership"]:checked').val() || 0,
        project_mgt: $('input[name="project_mgt"]:checked').val() || 0,
        res_allocating: $('input[name="res_allocating"]:checked').val() || 0
    };
    
    try {
        let response = await send_performancePost('update indicators', formData);
        if (response) {
            let res = JSON.parse(response);
            if (res.error) {
                toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
                toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 1000 }).then(() => {
                    // Reset form and close modal
                    $('#editIndicatorsForm')[0].reset();
                    $('#edit_indicators').modal('hide');
                    load_indicators();
                });
            }
        } else {
            console.log('Failed to update indicator.');
        }
    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }
}

function handleAppraisals() {
    $('#slcDepartment').on('change', function() {
        let department_id = $(this).val();
        let formData = {department_id:department_id}
        try {
            send_performancePost('get indicator4Appraisals', formData).then(response => {
                if (response) {
                    let res = JSON.parse(response);
                    if (!res.error && res.data) {
                        // Get the indicator data
                        let indicator = res.data;
                        let attributes = JSON.parse(indicator.attributes);
                        
                        // Update each indicator rating based on the returned data
                        if (attributes.business_pro) {
                            $(`input[name="indicator_business_pro"][value="${attributes.business_pro}"]`).prop('checked', true);
                        }
                        if (attributes.oral_com) {
                            $(`input[name="indicator_oral_com"][value="${attributes.oral_com}"]`).prop('checked', true);
                        }
                        if (attributes.leadership) {
                            $(`input[name="indicator_leadership"][value="${attributes.leadership}"]`).prop('checked', true);
                        }
                        if (attributes.project_mgt) {
                            $(`input[name="indicator_project_mgt"][value="${attributes.project_mgt}"]`).prop('checked', true);
                        }
                        if (attributes.res_allocating) {
                            $(`input[name="indicator_res_allocating"][value="${attributes.res_allocating}"]`).prop('checked', true);
                        }
                        
                        // Show a notification that indicators have been loaded
                        toaster.info('Indicators loaded for selected department', 'Info', { top: '20%', right: '20px', hide: true, duration: 2000 });
                    } else if (res.error) {
                        // Reset all indicators if no data found or there's an error
                        $('input[name^="indicator_"]').prop('checked', false);
                        if (res.msg) {
                            toaster.info(res.msg, 'Info', { top: '20%', right: '20px', hide: true, duration: 2000 });
                        }
                    }
                }
            });
        } catch (err) {
            console.error('Error occurred during indicator search:', err);
        }
    });
    
    // Initialize the appraisals form submission
    $('#addAppraisalsForm').on('submit', (e) => {
        handle_addAppraisalsForm(e.target);
        return false;
    });
}
