$().ready(function(){
    $("#login-button").bind("click",function () {
        var email = $("#sign-in-email").val();
        var password = $("#sign-in-password").val();
        email = email.toLocaleLowerCase();
        var valid = true;
        var errorText = "";
        $("#sign-in-error").fadeIn();
        if (password.length < 8){
            errorText = "Password too short. <br/>";
            $("#sign-in-password").attr("style","border-bottom-color:#D34E5C");
            valid = false;
        }
        if (!validateEmail(email)){
            errorText += "A valid email address is required.<br/>";
            $("#sign-in-email").attr("style","border-bottom-color:#D34E5C");
            valid = false;
        }
        $("#sign-in-error").html(errorText);
        $("#sign-in-error").fadeOut(5000);
        if (valid){
            $.post("user.php",{action:'login',email:email,password:password},function (result) {
                switch (result){
                    case "no match":
                        errorText += "Password and Email do not match.<br/>";
                        break;
                    case "no user":
                        errorText += "Email not registered.<br/>";
                        break;
                    case "user login":
                        window.location.href = "projects.php"; //Logged in page
                }
                displayError()
            })
        }
        function displayError() {
            $("#sign-in-error").html(errorText);
            $("#sign-in-error").fadeOut(5000);
        }

    })
});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}