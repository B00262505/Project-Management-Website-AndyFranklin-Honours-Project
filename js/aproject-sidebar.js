/**
 * Created by Andy on 30/10/2016.
 */
$().ready(function () {
    $("#expand-sidebar-button").bind("click",function () {
        $(".sidebar").animate({'width': 'toggle'});
        $(".project-area").toggleClass("col-md-offset-3");
        $(".project-area").toggleClass("col-md-9 col-md-12");
        $("#expand-sidebar-button").toggleClass("glyphicon-circle-arrow-left glyphicon-circle-arrow-right")
    });
    $("#user-add-user").bind("click",function () {
        $("#sidebar-error").html("");
        var userEmail = $("#sidebar-new-user-email").val();
        if (validateEmail(userEmail)){
            $.post('project.php',{action: "adduser",email:userEmail,permission:$("#user-add-user-select").val(),projectid:projectID},function (result) {
                if (result == "no user"){
                    $("#sidebar-error").html("No such user.").stop().fadeIn(100).fadeOut(5000);
                } else if (result=="user added"){
                    fillUsersSidebar();
                    $("#sidebar-error").html("User added.").stop().fadeIn(100).fadeOut(5000);
                } else if (result=="already member"){
                    $("#sidebar-error").html("User already member.").stop().fadeIn(100).fadeOut(5000);
                }
            });
        } else {
            $("#sidebar-error").html("Enter a valid email.").stop().fadeIn(100).fadeOut(5000);
        }

    });
    $("#user-remove-manager").bind("click",function () {
        var selected = [];
        $('#sidebar-managers input:checked').each(function() {
            selected.push($(this).val());
        });
        for (var i = 0; i < selected.length; i++){
            removeUserByEmail(selected[i])
        }
        fillUsersSidebar();
    });
    $("#user-remove-viewer").bind("click",function () {
        var selected = [];
        $('#sidebar-viewers input:checked').each(function() {
            selected.push($(this).val());
        });
        for (var i = 0; i < selected.length; i++){
            removeUserByEmail(selected[i])
        }
        fillUsersSidebar();
    });
    $("#user-remove-user").bind("click",function () {
        var selected = [];
        $('#sidebar-users input:checked').each(function() {
            selected.push($(this).val());
        });
        for (var i = 0; i < selected.length; i++){
            removeUserByEmail(selected[i])
        }
        fillUsersSidebar();
    });
    fillUsersSidebar();
});

function removeUserByEmail(email) {
    email = email.toLocaleLowerCase();
    $.post("project.php",{action:'removeuser',email:email},function (result) {
        var errorMessage = "";
        if (result=="deleted"){
            errorMessage = "User removed."
        } else {
            errorMessage = "ERROR: A log has been created."
        }
        $("#sidebar-error").html(errorMessage).stop().fadeIn(100).fadeOut(5000);
    })
}


var owner = [];
var managers = [];
var users = [];
var viewers = [];
function fillUsersSidebar() {
    owner = [];
    managers = [];
    users = [];
    viewers = [];
    $.post("project.php",{action:'getuserlist'},function (result) {
        var user = JSON.parse(result);
        for (var i=0; i<user.length; i++){
            switch (user[i].permissionID){
                case "2":
                    owner.push(user[i]);
                    break;
                case "3":
                    managers.push(user[i]);
                    break;
                case "4":
                    viewers.push(user[i]);
                    break;
                case "5":
                    users.push(user[i]);
                    break;
            }
        }
        var html="";
        for (var j=0; j<users.length; j++){
            html+=generateSidebarCheckboxHTML(users[j]);
        }
        $("#sidebar-users").html(html);
        html="";
        for (var k=0; k<managers.length; k++){
            html+=generateSidebarCheckboxHTML(managers[k]);
        }
        $("#sidebar-managers").html(html);
        html="";
        for (var l=0; l<viewers.length; l++){
            html+=generateSidebarCheckboxHTML(viewers[l]);
        }
        $("#sidebar-viewers").html(html);
    })
}

function generateSidebarCheckboxHTML(user) {

    return "<div class='checkbox'>" +
        "<label>" +
        "<input type='checkbox' value='"+ user.email + "'>" +
        "<span>"+ user.firstname +" "+ user.lastname+"</span>" +
        "</label>" +
        "</div>"

}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}