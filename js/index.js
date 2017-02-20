/**
 * Created by Andy on 04/10/2016.
 */

$().ready(function(){
    $(".next-page-btn").click(function() {
        $('html,body').animate({
                scrollTop: $(".index-projects").offset().top},
            'slow');
    });
    $("#sign-up-button").bind("click",function(){
        var firstName = $("#sign-up-first-name").val();
        var lastName = $("#sign-up-last-name").val();
        var email = $("#sign-up-email").val();
        email = email.toLocaleLowerCase();
        var password = $("#sign-up-password").val();
        var valid = true;
        var errorText = "";
        $("#sign-up-error").fadeIn();
        $("#sign-up-first-name").attr("style","border-bottom-color:#eee");
        $("#sign-up-last-name").attr("style","border-bottom-color:#eee");
        $("#sign-up-password").attr("style","border-bottom-color:#eee");
        $("#sign-up-email").attr("style","border-bottom-color:#eee");

        if (password.length < 8){
            valid = false;
            errorText = "Password too short. <br/>";
            $("#sign-up-password").val("");
            $("#sign-up-password").attr("style","border-bottom-color:#D34E5C");
        }
        if (firstName==""){
            valid = false;
            $("#sign-up-first-name").attr("style","border-bottom-color:#D34E5C");
            errorText += "Your first name is required.<br/>";
        }
        if (lastName==""){
            $("#sign-up-last-name").attr("style","border-bottom-color:#D34E5C");
            errorText += "Your last name is required.<br/>";
        }
        if (!validateEmail(email)){
            valid = false;
            $("#sign-up-email").attr("style","border-bottom-color:#D34E5C");
            errorText += "A valid email address is required.<br/>";
        }
        $("#sign-up-error").html(errorText);
        $("#sign-up-error").fadeOut(5000);
        if (valid){
            $.post("user.php",{action: 'register', firstName : firstName,lastName: lastName, email: email, password: password},function (result) {
                console.log(result);
                switch (result) {
                    case "registered":
                        $.post("user.php",{action:'login',email:email,password:password},function (result) {
                            switch (result){
                                case "no match":
                                    break;
                                case "user login":
                                    window.location.href = "projects.php"; //Logged in page
                            }
                        });
                        break;
                    case "email exists":
                        $("#sign-up-error").text("Email already exists.");
                        $("#sign-up-error").fadeOut(5000);
                        $("#sign-up-email").val("");
                        $("#sign-up-email").attr("style","border-bottom-color:#D34E5C");
                        break;
                }
            })
        }
        // console.log(firstName,lastName,email,password);
    });
});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}