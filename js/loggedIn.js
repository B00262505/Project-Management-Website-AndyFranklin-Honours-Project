/**
 * Created by Andy on 20/10/2016.
 */
var email = userRow[0];
var firstName = userRow[1];
var lastName = userRow[2];

$().ready(function () {
    $("#logged-in-user").html(firstName + " " + lastName);

    $("#logout-button").bind("click",function () {
        $.post("user.php",{action:'logout'},function () {
            window.location.href = "index.html";
        })
    })
});