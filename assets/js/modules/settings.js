async function send_settingsPost(str, data) {
    let [action, endpoint] = str.split(' ');

    try {
        const response = await $.post(`${base_url}/app/settings_controller.php?action=${action}&endpoint=${endpoint}`, data);
        return response;
    } catch (error) {
        console.error('Error occurred during the request:', error);
        return null;
    }
}

async function change_settings(type, isOption = false) {
	let data = await get_setting(type);
    console.log(data)
    let modal = $('#change_setting');
    if(data) {
    	let res = JSON.parse(data);
    	$(modal).find('.settingType').val(type);
    	$(modal).find('.settingSection').val(res.section);
    	$(modal).find('.settingRemarks').val(res.remarks);
    	$(modal).find('.settingDetails').val(res.details);
    	$(modal).find('.settingValue').val(res.value);

    	if(res.remarks != 'required') $(modal).find('.settingValue').removeClass('validate')
    }

	$(modal).modal('show');
}

async function get_setting(type) {
	let data = {type};
	let response = await send_settingsPost('get setting', data);
	return response;
}

$(document).on('submit', '.changeSettingForm', async (e) => {
	let form = $(e.target);
	clearErrors();
    let error = validateForm(form);
    if (error) return false;

	let settingType = $(form).find('.settingType').val();
	let settingSection = $(form).find('.settingSection').val();
	let settingRemarks = $(form).find('.settingRemarks').val();
	let settingDetails = $(form).find('.settingDetails').val();
	let settingValue = $(form).find('.settingValue').val();

	let formData = {
        type: settingType,
        details:settingDetails,
        value:settingValue,
        section:settingSection,
        remarks: settingRemarks
    };

    try {
        let response = await send_settingsPost('update setting', formData);
        console.log(response)
        if (response) {
            let res = JSON.parse(response)
            $('#add_project').modal('hide');
            if(res.error) {
            	toaster.warning(res.msg, 'Sorry', { top: '30%', right: '20px', hide: true, duration: 5000 });
            } else {
            	toaster.success(res.msg, 'Success', { top: '20%', right: '20px', hide: true, duration:2000 }).then(() => {
            		// location.reload();
            		load_projects();
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
})