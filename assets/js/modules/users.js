async function send_userPost(str, data) {
    let [action, endpoint] = str.split(' ');

    try {
        const response = await $.post(`${base_url}/app/users_controller.php?action=${action}&endpoint=${endpoint}`, data);
        return response;
    } catch (error) {
        console.error('Error occurred during the request:', error);
        return null;
    }
}
function load_users() {
	var datatable = $('#usersDT').DataTable({
		// let datatable = new DataTable('#companyDT', {
	    "processing": true,
	    "serverSide": true,
	    "bDestroy": true,
	    "columnDefs": [
	        { "orderable": false, "searchable": false, "targets": [4] }  // Disable search on first and last columns
	    ],
	    "serverMethod": 'post',
	    "ajax": {
	        "url": "./app/users_controller.php?action=load&endpoint=users",
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
	        { title: "Full Name", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.full_name}</span>
	                </div>`;
	        }},

	        { title: "Phone Numbers", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.phone}</span>
	                </div>`;
	        }},

	        { title: "Emails", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.email}</span>
	                </div>`;
	        }},

	        { title: "Username", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.username}</span>
	                </div>`;
	        }},

	        { title: "Role", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.role}</span>
	                </div>`;
	        }},

	        { title: "Status", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.status}</span>
	                </div>`;
	        }},

	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
	            		<a href="${base_url}/user/edit/${row.user_id}" class="fa edit_companyInfo smt-5 cursor smr-10 fa-pencil"></a>
	            		<span data-recid="${row.id}" class="fa delete_company smt-5 cursor fa-trash"></span>
	                </div>`;
	        }},
	    ]
	});

	return false;
}
document.addEventListener("DOMContentLoaded", function() {
	load_users();
	$('#checkAll').on('change', (e) => {
		if($(e.target).is(':checked')) {
			$('input.user_permission').attr('checked', true)
			$('input.user_permission').prop('checked', true)
		} else {
			$('input.user_permission').attr('checked', false)
			$('input.user_permission').prop('checked', false)
		}
	})

	
	$('input.user_permission').on('change', (e) => {
		let checkAll = true;
		$('input.user_permission').each((i, el) => {
			if($(el).is(':checked')) {
				// checkAll = true;
			} else {checkAll  = false}
		})
		console.log(checkAll)
		if(!checkAll) {
			$('#checkAll').attr('checked', false)
			$('#checkAll').prop('checked', false)
		}
	})

	/*$('#searchEmployee').on('keyup', async (e) => {
		let search = $(e.target).val();
		let searchFor = 'create-user';

		let formData = {search:search, searchFor:searchFor}
		if(search) {
			try {
		        let response = await send_userPost('search employee4UserCreate', formData);
		        console.log(response)
		        let res = JSON.parse(response);
		        $('.search_result.employee').css('display', 'block')
		        $('.search_result.employee').html(res.data)
		    } catch (err) {
		        console.error('Error occurred during form submission:', err);
		    }
		}
	})*/

	// Add user
	$('#addUserForm').on('submit', (e) => {
		handle_addUserForm(e.target);
		return false
	})

	// Edit user
	$('#editUserForm').on('submit', (e) => {
		handle_editUserForm(e.target);
		return false
	})
	

});	

function handleUser4CreateUser(employee_id, full_name) {
	$('.employee_id4CreateUser').val(employee_id)
	$('#searchEmployee').val(full_name)
	$('.search_result.employee').css('display', 'none')
    $('.search_result.employee').html('')
    return false
}

async function handle_addUserForm(form) {
	clearErrors();
    let full_name 	= $(form).find('#searchEmployee').val();
    let phone 		= $(form).find('#phone').val();
    let email 		= $(form).find('#email').val();
    let username 		= $(form).find('#username').val();
    let password 		= $(form).find('#password').val();
    let systemRole 		= $(form).find('#systemRole').val();
    let permissions 	= [];

    $('.user_permission:checked').each((i, el) => {
    	permissions.push($(el).val());
    })

    console.log(permissions)
    // return false;


    // Input validation
    let error = false;
    error = !validateField(full_name, `Full name is required`, 'full_name') || error;
    error = !validateField(username, `Username is required`, 'username') || error;
    error = !validateField(password, `Password is required`, 'password') || error;
    error = !validateField(systemRole, `Please select user role`, 'systemRole') || error;

    if (error) return false;

    let formData = {
        full_name: full_name,
        phone:phone,
        email:email,
        username: username,
        password: password,
        systemRole: systemRole,
        permissions: permissions,
    };

    try {
        let response = await send_userPost('save user', formData);
        console.log(response)

        if (response) {
            let res = JSON.parse(response)
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            	}).then((e) => {
            		window.location = `${base_url}/users`;
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save user.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }

    return false
}
async function handle_editUserForm(form) {
	clearErrors();
	let full_name 	= $(form).find('#searchEmployee').val();
    let phone 		= $(form).find('#phone').val();
    let email 		= $(form).find('#email').val();
    let username 		= $(form).find('#username').val();
    let systemRole 		= $(form).find('#systemRole').val();
    let permissions 	= [];
	let user_id 		= $(form).find('#user_id4Edit').val();
    let slcStatus 		= $(form).find('#slcStatus').val();

    $('.user_permission:checked').each((i, el) => {
    	permissions.push($(el).val());
    })

    console.log(permissions)
    // return false;


    // Input validation
    let error = false;
    error = !validateField(full_name, `Full name is required`, 'full_name') || error;
    error = !validateField(username, `Username is required`, 'username') || error;
    // error = !validateField(systemRole, `Please select user role`, 'systemRole') || error;

    if (error) return false;

    let formData = {
        full_name: full_name,
        phone:phone,
        email:email,
        username: username,
        systemRole: systemRole,
        permissions: permissions,
        user_id:user_id,
        slcStatus:slcStatus
    };

    try {
        let response = await send_userPost('update user', formData);
        console.log(response)

        if (response) {
            let res = JSON.parse(response)
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            	}).then((e) => {
            		window.location = `${base_url}/users`;
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save user.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }

    return false
}