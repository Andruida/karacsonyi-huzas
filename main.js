const validateEmail = (email) => {
    return String(email)
        .toLowerCase()
        .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
};

function submit() {
    var emailField = $("#email")
    var nameField = $("#name")
    var wishField = $("#wish")

    var valid = true;

    if (!validateEmail(emailField.val())) {
        valid = false
        emailField.addClass("is-invalid")
    } else {
        emailField.removeClass("is-invalid")
    }

    if (nameField.val().length < 2) {
        valid = false
        nameField.addClass("is-invalid")
    } else {
        nameField.removeClass("is-invalid")
    }

    if (wishField.val().length < 2) {
        valid = false
        wishField.addClass("is-invalid")
    } else {
        wishField.removeClass("is-invalid")
    }

    if (!valid) return false;

    $.ajax({
        method: "POST",
        data: {
            email: emailField.val(),
            name: nameField.val(),
            wish: wishField.val()
        },
        success: function(response) {
            var modal = new bootstrap.Modal("#successModal", {})
            modal.show();

            emailField.val("")
            nameField.val("")
            wishField.val("")
        },
        error: function(jqXHR, textStatus, errorThrown) {
            var modal = new bootstrap.Modal("#errorModal", {})
            modal.show();
        }
    })
}