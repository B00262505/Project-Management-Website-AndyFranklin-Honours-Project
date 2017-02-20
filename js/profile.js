/**
 * Created by Andy on 20/10/2016.
 */

$().ready(function () {
    $("#profile-email").html(email);
    $("#profile-firstname").html(firstName);
    $("#profile-lastname").html(lastName);


    $("#profile-edit-fname").bind("click",function () {
        if ($("#profile-edit-fname").text() == "Edit") {
            $("#profile-edit-fname").text("Save");
            $("#profile-firstname").html("<input class='profile-input' type='text' placeholder='"+firstName+"'/>");
        } else {
            $("#profile-error-fname").text("");
            var input = $("#profile-firstname").children().val();
            if (input ==""){
                $("#profile-error-fname").text("Must contain a value.");
            } else {
                $.post("user.php",{action:'update-fname',firstName:input},function (result) {
                    if (result == "updated"){
                        $("#profile-edit-fname").text("Edit");
                        $("#profile-firstname").html(input);
                    }
                });
            }
        }
    });
    $("#profile-edit-lname").bind("click",function () {
        if ($("#profile-edit-lname").text() == "Edit") {
            $("#profile-edit-lname").text("Save");
            $("#profile-lastname").html("<input class='profile-input' type='text' placeholder='" + lastName + "'/>");
        } else {
            $("#profile-error-lname").text("");
            var input = $("#profile-lastname").children().val();
            if (input ==""){
                $("#profile-error-lname").text("Must contain a value.");
            } else {
                $.post("user.php",{action:'update-lname',lastName:input},function (result) {
                    if (result == "updated"){
                        $("#profile-edit-lname").text("Edit");
                        $("#profile-lastname").html(input);
                    }
                });
            }
        }
    });

    $("#profile-edit-password").bind("click",function () {
        if ($("#profile-edit-password").text() == "Edit") {
            $("#profile-edit-password").text("Save");
            $("#profile-password").html("<input class='profile-input' type='password' placeholder='New Password' id='profile-new-password1'/>" +
                "<input type='password' class='profile-input' placeholder='Confirm Password' id='profile-new-password2'/>");
        } else { //SAVE
            $("#profile-error-password").text("");
            var input1 = $("#profile-new-password1").val();
            var input2 = $("#profile-new-password2").val();
            if (input1.length < 8){
                $("#profile-error-password").text("Password too short.");
            } else if (input1!==input2) {
                $("#profile-error-password").text("Passwords must match.");
            } else {
                $.post("user.php",{action:'update-password',password:input1},function (result) {
                    if (result == "updated"){
                        $("#profile-edit-password").text("Edit");
                        $("#profile-password").html("*******************");
                    }
                });
            }
        }

    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
});