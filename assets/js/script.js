document.addEventListener("DOMContentLoaded", function() {
	$('.datepicker').each(function(index, element) {
        new Pikaday({
            field: element,
            position: 'bottom left',
            reposition: false
        });
    });

    // Customer tooltip
    $(document).on('click', '.info-tooltip', (e) => {
        $(e.currentTarget).find('.tooltip-div').show(300);
        e.stopPropagation();
        console.log($(e.currentTarget))
    })

    $(document).on('click', '.tooltip-div', (e) => {
        e.stopPropagation();
    });

    $(document).on('click', (e) => {
        $('.tooltip-div').hide(300)
    })


    $(document).on('click', '.hide-tooltip', (e) => {
        e.stopPropagation(); // Prevent the event from bubbling to parent elements
        $('.tooltip-div').hide(300);
    });





    // Login User
    $('#userLoginForm').on('submit', (e) => {
        e.preventDefault();  // Prevent the default form submission

        let form = $(e.target);
        let username = $(form).find('#inputEmailAddress').val();
        let password = $(form).find('#inputChoosePassword').val();

        let error = false;
        error = !validateField(username, `Username is required`, 'inputEmailAddress') || error;
        error = !validateField(password, `Password is required`, 'inputChoosePassword') || error;

        if (error) return false;

        $.ajax({
            url: 'app/auth.php?action=login',
            method: 'POST',
            data: {
                username: username,
                password: password
            },
            success: function(response) {
                console.log(response)
                // return false;
                let res = JSON.parse(response)
                console.log(res)
                if(res.error) {
                    toaster.error("Incorrect username/email or password", 'Sorry', { top: '5%', right: '0%', center: true, hide: false });
                    return false;
                }
                
                location.href = res.land
            },
            error: function(xhr, status, error) {
                // Handle error
                console.log('Login failed: ' + error);
                // You can display an error message to the user
            }
        });

        return false;
    });

	

});