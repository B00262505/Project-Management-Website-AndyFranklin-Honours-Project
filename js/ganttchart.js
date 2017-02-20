var taskStatus = {
    "SUCCEEDED": "bar",
    "FAILED": "bar-failed",
    "RUNNING": "bar-running"
};

var taskNames = [];
$().ready(function () {
    tasks = [];
    tasksFromServer();
    waitTimer();
});

function waitTimer(){
    if (tasks.length == 0){
        setTimeout(waitTimer,100);
    } else {
        initTree();
        createGantt();
        bindMouseOver();
        pieWait();
        initPie();
    }

}

function createPieChart(){
    
}

function createGantt(){
    for (var i=0; i<tasks.length;i++){
        taskNames.push(tasks[i].title);

        var now = new Date();
        now.setHours(0,0,0,0);
        if (tasks[i].completed){
            tasks[i].status = "SUCCEEDED";
        } else if (tasks[i].endDate < now){
            tasks[i].status = "FAILED";
        } else {
            tasks[i].status = "RUNNING";
        }
    }

    var format = "%d/%m";
    var gantt = d3.gantt().taskTypes(taskNames).taskStatus(taskStatus).tickFormat(format);
    gantt(tasks);
}

function bindMouseOver() {
    for (var i = 0; i<tasks.length; i++){
        $("#"+tasks[i].id+"-task-rect").hover((function (i) {
            return function (e) {
                $("#gantt-info-heading").html(tasks[i].title);
                var statusHTML = "";
                if (tasks[i].status == "SUCCEEDED"){
                    statusHTML += "This task has been completed. <br/>"
                } else if (tasks[i].status == "FAILED"){
                    statusHTML += "This task is overdue.<br/>"
                } else if(tasks[i].status == "RUNNING"){
                    statusHTML += "This task has not yet been completed.<br/>"
                }
                var bodyHTML ="<br/>";

                bodyHTML += "Task began on: "+tasks[i].startDate.toDateString();
                bodyHTML += "<br/>Task deadline: "+tasks[i].endDate.toDateString();
                if (tasks[i].creator != ""){
                    bodyHTML += "<br/>Task created by: "+tasks[i].creator;
                }

                bodyHTML += "<br/>Task deadline: "+tasks[i].endDate.toDateString();
                bodyHTML += "<br/><hr/><br/>"+tasks[i].description;
                var userListHTML = "";

                userListHTML += "This task is worked by: <br/>";
                if (tasks[i].id != 0){
                    $.post("project.php", {
                        action: 'gettaskusers',
                        taskid: tasks[i].id
                    }, function (result) {
                        result = JSON.parse(result);
                        taskUserList = result;

                        for (var j=0;j<taskUserList.length;j++){
                            if (taskUserList.length !== 1 && j !==0){ //only "/" on 2nd++ and when more than  one user
                                userListHTML += " / ";
                            }
                            userListHTML += taskUserList[j].firstname +" "+taskUserList[j].lastname;
                        }
                        if (userListHTML == "This task is worked by: <br/>"){
                            userListHTML = "This task is worked by: None<br/>";
                        }

                        $("#gantt-info-body").html(statusHTML + userListHTML + bodyHTML);
                        $("#gantt-info-box").stop().fadeIn();
                    })
                } else {
                    if (userListHTML == "This task is worked by: <br/>"){
                        userListHTML = "This task is worked by: None<br/>";
                    }
                    $("#gantt-info-body").html(statusHTML + userListHTML + bodyHTML);
                    $("#gantt-info-box").stop().fadeIn();
                }

                var pageWidth = $("#body").width();
                if (e.pageX > pageWidth){
                    $("#gantt-info-box").css("left",e.pageX-300);
                } else {
                    $("#gantt-info-box").css("left",e.pageX);
                }
                $("#gantt-info-box").css("top",e.pageY);
            }
        })(i),function () {
            $("#gantt-info-box").stop().fadeOut();
        });
    }
}