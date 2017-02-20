/**
 * Created by Andy on 30/09/2016.
 */

$("#new-project-button").bind("click",function(){
    $("#new-project-div").stop().slideToggle("slow");
});

function isEmpty(jqObject) {
    return (jqObject.val()=="")
}

$("#new-project-save").bind("click",function () {
    var newProjectTitle = $("#new-project-title");
    var newProjectDesc = $("#new-project-description");
    var newProjectSDate = $("#new-project-startdate");
    var newProjectEDate = $("#new-project-enddate");

    var valid = true;
    var errorText = "";
    if (isEmpty(newProjectTitle)){
        errorText += "The project requires a title. <br/>";
        valid = false;
    }
    if (isEmpty(newProjectDesc)){
        errorText += "The project requires a description.<br/>";
        valid = false;
    }
    if (isEmpty(newProjectSDate)){
        errorText += "The project requires a start date.<br/>";
        valid = false;
    }
    if (isEmpty(newProjectEDate)){
        errorText += "The project requires an end date.<br/>";
        valid = false;
    }
    if (!valid){
        $("#new-project-error").html(errorText).stop().fadeIn().fadeOut(5000);
    } else { //Save to database
        $(this).prop("disabled",true);

        $.post("project.php",{action:'create',title:newProjectTitle.val(),desc:newProjectDesc.val(),startDate:newProjectSDate.val(),endDate:newProjectEDate.val()},function (result) {
            if (result !== "error"){
                //clear form
                $(this).prop("disabled",false);
                $("#new-project-div").stop().slideToggle("slow");
                $("#new-project-error").html("");
                $("#new-project-success").html("Project added.").stop().fadeIn().fadeOut(5000);
                project = JSON.parse(result);
                displayProjects();
                rebindViewMore();
                clearForm();
            }
        })
    }
});

function clearForm(){
    $("#new-project-title").val("");
    $("#new-project-description").val("");
    $("#new-project-startdate").val("");
    $("#new-project-enddate").val("");
}

function rebindViewMore(){
    $(".view-project-button").unbind();
    $(".view-project-button").bind("click",function(){
        $.post('project.php',{
            action:'setprojectid',
            projectid:$(this).siblings('input[name="project_id"]').val()
        },function (result) {
            if (result =="success"){
                window.location.href = "aproject.php";
            }
        })
    });
}

Date.daysBetween = function( date1, date2 ) {
    //Get 1 day in milliseconds
    var one_day=1000*60*60*24;

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();

    // Calculate the difference in milliseconds
    var difference_ms = date2_ms - date1_ms;

    // Convert back to days and return
    return Math.round(difference_ms/one_day);
};

function displayProjects() {
    var projectsHTML = "<div class='row'>";

    var count = 0;

    for (var i =0; i < project.projects.length; i++){

        console.log(count);
        var title = project.projects[i].title;
        var desc = project.projects[i].description;
        var startDate = new Date( project.projects[i].startDate);
        var endDate = new Date( project.projects[i].endDate);
        var projectID = project.projects[i].id;

        var timeDifference = Date.daysBetween(new Date(),endDate);
        var classColour = "";
        if (timeDifference <= 0){
            classColour = "current-project-time-0day";
        } else if (timeDifference <= 3){
            classColour = "current-project-time-3day";
        } else {
            classColour = "current-project-time";
        }
        console.log(new Date()+"  ::  "+endDate);
        var projectHTML = '<div class="col-md-4"><div class="well">' +
            '<h3>'+title+'</h3>' +
            '<h4><i>Deadline: '+ endDate.toDateString()+'</i></h4>'+
            '<p class="'+ classColour +'"><b>'+timeDifference+' days from now.</b></p>' + // <p><span>1</span> Weeks, <span>5</span> Days, <span>2</span> Hours, <span>20</span> Minutes, <span>50</span> Seconds.</p>
            '<p>'+ desc +'</p>' +
            '<p><input type="hidden" name="project_id" value="'+ projectID+'"/>' +
            '<a class="btn btn-default view-project-button" role="button" >View details &raquo;</a></p>'+
            '</div></div>';
        if (count == 3){
            //New line
            projectsHTML += "</div><div class='row'>";
            count = 0;
        }
        count++;
        projectsHTML += projectHTML;
    }

    $("#current-user-projects").html(projectsHTML);
}

$().ready(function () {
    displayProjects();

    $("#new-project-clear").bind("click",function(){
        clearForm();
    });
    $(".view-project-button").bind("click",function(){
        $.post('project.php',{
            action:'setprojectid',
            projectid:$(this).siblings('input[name="project_id"]').val()
        },function (result) {
            if (result =="success"){
                window.location.href = "aproject.php";
            }
        })
    });
});