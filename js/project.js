/**
 * Created by Andy on 28/10/2016.
 */
var projectTitle = "";
var projectDesc = "";
var projectEndDate = "";
var projectStartDate = "";
var projectCompleted = "";
var projectID = "";

var project = "";
$().ready(function () {
    $.post("project.php",{action:'getprojectbyid'},function (result) {
        project = JSON.parse(result);
        projectTitle = project.project.title;
        projectDesc = project.project.description;
        projectEndDate = new Date(project.project.endDate);
        projectStartDate = new Date(project.project.startDate);
        projectCompleted = project.project.completed;
        projectID = project.project.id;

        
        document.title = projectTitle;
        $("#current-project-title").html("<b>"+projectTitle + "  &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; " +
            "Deadline: "+projectEndDate.toDateString()+"</b>");
        $("#current-project-dates").html("Project began on: "+ projectStartDate.toDateString() +"." +
            " There is <span id='current-project-time'>" + Date.daysBetween(new Date(), projectEndDate) + "</span> days remaining.");

        $("#current-project-description").html(projectDesc);

    })
});

function setProjectVariables() {
    $.post("project.php",{action:'getprojectbyid'},function (result) {
        project = JSON.parse(result);
        projectTitle = project.project.title;
        projectDesc = project.project.description;
        projectEndDate = new Date(project.project.endDate);
        projectStartDate = new Date(project.project.startDate);
        projectCompleted = project.project.completed;
        projectID = project.project.id;
    });
}

function setProjectDaysLeft() {
    
}

Date.daysBetween = function( date1, date2 ) {
    //Get 1 day in milliseconds
    date1 = new Date(date1);
    date2 = new Date(date2);
    var one_day=1000*60*60*24;
    // Convert both dates to milliseconds
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();

    // Calculate the difference in milliseconds
    var difference_ms = date2_ms - date1_ms;

    // Convert back to days and return
    return Math.round(difference_ms/one_day);
};