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

			{ title: "Reports to", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.reports_to}</span>
	                </div>`;
	        }},

	        { title: "Status", data: null, render: function(data, type, row) {
	            return `<div>
	            		<span>${row.status}</span>
	                </div>`;
	        }},
			// <span data-recid="${row.user_id}" onclick="return deleteUser(${row.user_id})"  class="fa  smt-5 cursor fa-trash"></span>
	        { title: "Action", data: null, render: function(data, type, row) {
	            return `<div class="sflex scenter-items">
					<a href="${base_url}/user/show/${row.user_id}" class="fa  smt-5 cursor smr-10 fa-eye"></a>
					<a onclick="return editUserModal(${row.user_id})" class="fa  smt-5 cursor smr-10 fa-pencil"></a>
					
				</div>`;
	        }},
	    ]
	});


	return false;
}
async function editUserModal(id) {
	let response = await send_userPost('get user', {id:id});
	// console.log(response)
	let modal = $('#editUser');
	if (response) {
		let res = JSON.parse(response)[0]
		console.log(res)
		$(modal).find('#full_name4Edit').val(res.full_name)
		$(modal).find('#phone4Edit').val(res.phone)
		$(modal).find('#email4Edit').val(res.email)
		$(modal).find('#sysRole4Edit').val(res.role)
		$(modal).find('#username4Edit').val(res.username)
		$(modal).find('#slcStatus').val(res.status)
		$(modal).find('#userId4Edit').val(res.user_id)
		$(modal).find('#reportsTo').find('option').prop('selected', false)
		console.log(res.reports_to)
		if(res.reports_to && res.reports_to.length > 0) {
			res.reports_to.forEach((item) => {
				console.log(item)
				$(modal).find('#reportsTo').find('option[value="' + item + '"]').prop('selected', true)
			})
		}

		$('.my-select').selectpicker('refresh');
	}

	$('#editUser').modal('show');
}
document.addEventListener("DOMContentLoaded", function() {
	load_users();
	$(document).on('change', '#checkAll', (e) => {
		if($(e.target).is(':checked')) {
			$('input.role_permission').attr('checked', true)
			$('input.role_permission').prop('checked', true)

			$('input.module').attr('checked', true)
			$('input.module').prop('checked', true)
		} else {
			$('input.role_permission').attr('checked', false)
			$('input.role_permission').prop('checked', false)

			$('input.module').attr('checked', false)
			$('input.module').prop('checked', false)
		}
	})

	$(document).on('change', '.role_permission', (e) => {
		let checkAll = true;
		$('.role_permission').each((i, el) => {
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
	
	$('#changePasswordForm').on('submit', async (e) => {
		console.log(e.target)
		e.preventDefault();
		handle_changePasswordForm(e.target);
		return false
	})

	$('.my-select').selectpicker({
	    noneResultsText: "No results found"
	});

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
    let full_name 	= $(form).find('#full_name').val();
    let phone 		= $(form).find('#phone').val();
    let email 		= $(form).find('#email').val();
	let sysRole 		= $(form).find('#sysRole').val();
    let username 		= $(form).find('#username').val();
    let password 		= $(form).find('#password').val();
	let reportsTo 		= $(form).find('#reportsTo').val();
 
    // return false;


    // Input validation
    let error = false;
    error = !validateField(full_name, `Full name is required`, 'full_name') || error;
	error = !validateField(sysRole, `Please select user role`, 'sysRole') || error;
    error = !validateField(username, `Username is required`, 'username') || error;
    error = !validateField(password, `Password is required`, 'password') || error;

    if (error) return false;

    let formData = {
        full_name: full_name,
        phone:phone,
        email:email,
        username: username,
        password: password,
        sysRole: sysRole,
		reportsTo: reportsTo,
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

	let full_name 	= $(form).find('#full_name4Edit').val();
	let phone 		= $(form).find('#phone4Edit').val();
	let email 		= $(form).find('#email4Edit').val();
	let username 	= $(form).find('#username4Edit').val();
	let sysRole 	= $(form).find('#sysRole4Edit').val();
	let user_id 	= $(form).find('#userId4Edit').val();
	let slcStatus 	= $(form).find('#slcStatus').val();
	let reportsTo 	= $(form).find('#reportsTo').val();
	let signature 	= $(form).find('#signature4Edit')[0]?.files[0] || null;

	// Validate input
	let error = false;
	error = !validateField(full_name, `Full name is required`, 'full_name') || error;
	error = !validateField(username, `Username is required`, 'username') || error;
	error = !validateField(sysRole, `Please select user role`, 'sysRole') || error;

	if (error) return false;

	let formData = new FormData();
	formData.append('full_name', full_name);
	formData.append('phone', phone);
	formData.append('email', email);
	formData.append('username', username);
	formData.append('sysRole', sysRole);
	formData.append('user_id', user_id);
	formData.append('slcStatus', slcStatus);
	formData.append('reportsTo', reportsTo);

	if (signature) {
		formData.append('signature', signature);
	}

	try {
		let response = await $.ajax({
			url: `${base_url}/app/users_controller.php?action=update&endpoint=user`,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
		});

		if (response) {
			let res = JSON.parse(response);
			if (res.error) {
				toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
			} else {
				toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration: 2000 })
					.then(() => window.location = `${base_url}/users`);
			}
		} else {
			console.log('Failed to save user.' + response);
		}
	} catch (err) {
		console.error('Error occurred during form submission:', err);
	}

	return false;
}


async function handle_changePasswordForm(form) {
	let newPassword =  $(form).find('#newPassword').val();
	let confirmNewPassword = $(form).find('#confirmNewPassword').val();
	if(newPassword != confirmNewPassword) {
		$('#newPassword').addClass('is-invalid')
		$('#confirmNewPassword').addClass('is-invalid')
		
		return false
	}

	console.log(newPassword, confirmNewPassword)

	
	let formData = {
		newPassword: newPassword,
		user_id: $(form).find('#user_id').val(),
	};

	try {
		let response = await send_userPost('update user_password', formData);
		console.log(response)

		if (response) {
			let res = JSON.parse(response)
			if(res.error) {
				toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
			} else {
				toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
				}).then((e) => {
					location.reload();
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

function deleteUser(id) {
	console.log(id)
}

function simplifyRoles() {
	$('#selectAll').on('change', function() {
		var isChecked = $(this).is(':checked');
		$('.form-check-input').prop('checked', isChecked);
	});

	$('.module').on('change', function() {
		var moduleId = $(this).attr('id');
		var isChecked = $(this).is(':checked');
		$('.' + moduleId).prop('checked', isChecked);
	});

	// If action clicked without module, check all actions
	$('.action').on('change', function() {
		var moduleId = $(this).data('module');
		var isChecked = $(this).is(':checked');
		if(isChecked) {
			$('#' + moduleId).prop('checked', true);
		} 
	});

	// form submit
	$('#addSystemRoleForm').on('submit', (e) => {
		handle_addRole(e.target);
		return false
	})

	// Edit role
	$('#editSystemRoleForm').on('submit', (e) => {
		handle_editRole(e.target);
		return false
	})
}

async function handle_addRole(form) {
    var form = $(form);
	let error = validateForm(form)

	let name 		= $(form).find('#roleName').val();
	let reportsTo 	= $(form).find('#reportsTo').val();
    let actions 	= [];

	$('input.action:checked').each((i, el) => {
		let moduleId = $(el).data('module');
		let action = $(el).val();
		actions.push(action);
	});

	console.log(actions)

	if (error) return false;

	let formData = {
        name: name,
        actions: actions,
		reportsTo: reportsTo
    };

	// console.log(formData)

	// return false;

	form_loading(form);

	try {
        let response = await send_userPost('save role', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 3000 }).then(() => {
            		location.reload();
            	});;
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to save state.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }

	return false;
}

async function handle_editRole(form) {
	var form = $(form);
	let error = validateForm(form)

	let name 		= $(form).find('#roleName4Edit').val();
	let role_id 	= $(form).find('#role_id').val();
	let reportsTo 	= $(form).find('#reportsTo').val();
    let actions 	= [];

	$(form).find('input.action:checked').each((i, el) => {
		let moduleId = $(el).data('module');
		let action = $(el).val();
		actions.push(action);
	});

	console.log(actions)

	if (error) return false;

	let formData = {
        name: name,
		id: role_id,
        actions: actions,
		reportsTo: reportsTo
    };

	form_loading(form);

	try {
        let response = await send_userPost('update role', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 3000 }).then(() => {
            		location.reload();
            	});;
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
            		location.reload();
            	});
            	console.log(res)
            }
        } else {
            console.log('Failed to edit role.' + response);
        }

    } catch (err) {
        console.error('Error occurred during form submission:', err);
    }

	return false;
}

async function deleteRole(id) {
	swal({
        title: "Are you sure?",
        text: "You are going to delete this role!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(async (willDelete) => {
        if (willDelete) {
            try {
				let response = await send_userPost('delete role', {id:id});
				console.log(response)
				if (response) {
					let res = JSON.parse(response)
					if(res.error) {
						toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 3000 }).then(() => {
							location.reload();
						});;
					} else {
						toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:1000 }).then(() => {
							location.reload();
						});
						console.log(res)
					}
				} else {
					console.log('Failed to delete role.' + response);
				}
		
			} catch (err) {
				console.error('Error occurred during form submission:', err);
			}		
        }
    });

	return false;
}

async function editRoleModal(id) {
	let response = await send_userPost('get role4Edit', {id:id});
	console.log(response)
	if (response) {
		let res = JSON.parse(response)
		if(!res.error) {
			$('#editRole').find('.modal-body').html(res.data)
			$('.my-select').selectpicker({
				noneResultsText: "No results found"
			});
		
		}
	}

	$('#editRole').modal('show');
}
function changePasswordModal(id) {
	let modal = $('.modal#changePassword')
	$(modal).find('#user_id').val(id)
	$(modal).modal('show');
}