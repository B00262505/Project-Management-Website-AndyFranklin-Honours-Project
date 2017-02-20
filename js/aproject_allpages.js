/**
 * Created by Andy on 31/10/2016.
 */
//Noticeboard Messages New ~?

$().ready(function () {

    var projectTitle = "";
    var projectDesc = "";
    var projectEndDate = "";
    var projectStartDate = "";
    var projectCompleted = "";
    var projectID = "";
    var project = "";

    $.post("project.php",{action:'getprojectbyid'},function (result) {
        project = JSON.parse(result);
        projectTitle = project.project.title;
        projectDesc = project.project.description;
        projectEndDate = new Date(project.project.endDate);
        projectStartDate = new Date(project.project.startDate);
        projectCompleted = project.project.completed;
        projectID = project.project.id;
    });

    $.post("message.php",{action:"getprojectmessagescount"},function(result){
        message = JSON.parse(result);
        if (localStorage.getItem("messageCount-"+projectID)!=message.length && localStorage.getItem("messageCount-"+projectID)!==null){

            console.log(localStorage.getItem("messageCount"),message.length);
            $("#notice-board-glyphicon").toggleClass("glyphicon badge");
            $("#notice-board-glyphicon").toggleClass("glyphicon-envelope");
            if ($("#notice-board-glyphicon").hasClass("badge")){
                $("#notice-board-glyphicon").html(message.length - localStorage.getItem("messageCount"))
            }
        }
    });
});