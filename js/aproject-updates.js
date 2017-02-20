/**
 * Created by Andy on 02/02/2017.
 */

$().ready(function () {
    $.post("project.php", {
        action: 'getprojectlog'
    }, function (result) {
        $("#project-log").html(result)
    })
});