/**
 * Created by Andy on 03/11/2016.
 */

var tasks = [];

function Task(title, id, parentID, startDate, endDate, creator, userList, completed,description) {
    this.title = title;
    this.id = id;
    this.description = description;
    this.parentID = parentID; //Append to parent row the .expandable-row
    this.startDate = new Date(startDate);
    this.endDate = new Date(endDate);
    this.getDayDifference = function () {
        return Date.daysBetween(new Date(),this.endDate);
    };

    if (creator == 0 || creator == ""|| creator =="null null"){
        creator = "";
    }
    this.creator = creator;
    if (userList == undefined){
        userList = "None";
    }
    this.userList = userList;
    if (completed==1){
        completed = true;
    } else {
        completed =false;
    }
    this.status = "";
    this.completed = completed;
    this.children = [];
    this.closed = false;

    this.deleteTask = function () {
        if (this.children.length !=0){
            for (var i = 0; i < this.children.length; i++){
                this.children[i].deleteTask();
            }
        }
        var parentTask = getTaskByID(this.parentID);
        removeByAttr(parentTask.children,'id',this.id);

        removeByAttr(tasks, 'id', this.id);

        $.post("project.php", {
            action: 'deletetask',
            taskid: this.id
        }, function () {
            tasks = [];
            $("#tasks-table-body").html = "";
            tasksFromServer();
        })
    };
    this.completeTask = function () {
        var status = "";
        if($("#complete-task-checkbox").is(":checked")){
            //complete
            if (this.children.length !=0){
                for (var i = 0; i < this.children.length; i++){
                    this.children[i].completeTask();
                }
            }
            status = 1;
        } else {
            status = 0;
        }

        $.post("project.php", {
            action: 'settaskstatus',
            taskid: this.id,
            status: status
        }, function () {

        })
    };

    this.generateRow = function () {
        $("#tasks-table-body").append(generateTableRow(this));
        if (this.children.length != 0 ){
            for (var i = 0; i < this.children.length; i++){
                this.children[i].generateRow();
            }
        }
    }
}

var removeByAttr = function(arr, attr, value){
    var i = arr.length;
    while(i--){
        if( arr[i] && arr[i].hasOwnProperty(attr)&& (arguments.length > 2 && arr[i][attr] === value ) ){
            arr.splice(i,1);
        }
    }
};

function appendChildrenTasks(){
    for (var i = 0; i < tasks.length; i++){
        if ((tasks[i]).parentID != "null"){
            $.map(tasks, function (obj,index) {
                if (obj.id == tasks[i].parentID){
                    obj.children.push(tasks[i]);
                }
            });
        }
    }
}
function collapseTableRow(tableRowID){
    $(tableRowID)
    // http://blog.slaks.net/2010/12/animating-table-rows-with-jquery.html
        .children('td, th')
        .animate({ paddingBottom: 0, paddingTop: 0 })
        .wrapInner('<div />')
        .children()
        .slideToggle(function() { $(this).closest('tr') });
}

function expandTableRow(tableRowID) {
    $("#table-row-"+tableRowID)
    // http://blog.slaks.net/2010/12/animating-table-rows-with-jquery.html
        .children('td, th')
        .animate({ paddingBottom: "50px", paddingTop: "50px" }); //Set Default
        console.log("#table-row-"+tableRowID);
        $("#table-row-"+tableRowID).css("display","none");
        $("#table-row-"+tableRowID).prop("outerHTML",generateTableRow(getTaskByID(tableRowID)));
        $("#table-row-"+tableRowID+" div").slideDown();
    bindTableRowClick();
}

function closeChildren(taskID) {
    var task = getTaskByID(taskID);

    if (task.children.length > 0){
        for (var i=0;i<task.children.length;i++){
            collapseTableRow("#table-row-"+task.id);
            task.children[i].closed = true;
            closeChildren(task.children[i].id);
        }
    } else {
        collapseTableRow("#table-row-"+task.id);
        task.closed=true;
    }
}

function getTaskByID(taskID){
    var task = $.grep(tasks, function(e){ return e.id == taskID });
    task = task[0];
    return task;
}

var currentTask = "";
function bindTableRowClick() {
    $(".expandable-task").unbind("click");
    $(".expandable-task").bind("click",function () {
        var taskID = this.id.substr(10);
        var clickedTask = $.grep(tasks, function(e){ return e.id == taskID });
        clickedTask = clickedTask[0];
        for (var i = 0; i<clickedTask.children.length;i++){
            var childTask = clickedTask.children[i];
            if (childTask.closed){
                expandTableRow(childTask.id);
                childTask.closed = false;
            } else {
                closeChildren(clickedTask.children[i].id);
                childTask.closed = true;
            }

        }
    });
    bindTableViewMore();
}

function generateTableRow(task) {
    var now = new Date();
    now.setHours(0,0,0,0);

    $('#new-task-parentID').append('<option value="'+ task.id +'">'+task.id +' - ' + task.title+'</option>');
    var html = "<tr class='";
    if (task.children.length !== 0 ) {
        html += " expandable-task";
    }
    if (task.completed){
        html += " task-table-complete"
    } else if (task.endDate < now){
        html += " task-table-incomplete"
    } else {
        html += " task-table-running"
    }

    html += "' id='table-row-"+task.id+"'>";


    html += "<td><div>" + task.id + "";
    if (task.children.length !== 0 ){
        html += ' <img src="images/triangle-arrow.png" width="12px" height="12px">';
    }
    html += "</td>" +
            "<td><div>"+ task.title + "</div></td>" +
            "<td><div>"+ task.startDate.toLocaleDateString() +"</div></td>" +
            "<td><div>"+ task.endDate.toLocaleDateString() +"</div></td>" +
            "<td><div>"+ task.getDayDifference()+"</div></td>"+
            "<td><div>"+ task.creator +"</div></td>" +
            "<td><div id='userlist-" +task.id + "'>"+ task.userList + "</div></td>" + //Worked on by MULTIPLE users?
            "<td><div>"+
                "<div>" +
                    "<div>" +
                        task.completed +
                    "</div>" +
                "</div>"  +  //Progress
            "</div></td>" +

            //Controls
            "<td id='"+ task.id +"' class='"+task.id+"-view-more-d'>"+
                "<div class='container-fluid'>" +
                    "<div class='row'>" +
                        "<a class='tasks-viewmore' id='"+task.id +"-viewMore'><img src='images/viewmore.png' title='View Details' width='36x' height='36px'></a>" +
                    //     "<a class='tasks-editrow'><img src='images/pencil.png' title='Edit Row' width='24px' height='24px'></a>" +
                    //     "<a class='tasks-complete'><img src='images/tick.png' title='Mark Complete' width='24px' height='24px'></a>" +
                    // "</div>" +
                    // "<div class='row'>" +
                    //     "<a class='tasks-members'><img src='images/users.png' title='Assign Members' width='24px' height='24px'></a>" +
                    //     "<a class='tasks-rowdelete'><img src='images/table_row_delete.png' title='Delete Task' width='24px' height='24px'></a>" +
                    //     "<a class='tasks-rowinsert'><img src='images/table_row_insert.png' title='Insert Sub Task' width='24px' height='24px'></a>" +
                    "</div>" +
                "</div>" +
            "</td>";

    html += "</tr>";
    return html;
}

function tasksFromServer() {
    $("#tasks-table-body").html("");
    tasks = [];
    //Get project tasks
    $.post("project.php",{action:'getalltasks'},function (result) {
        //push project as task
        tasks.push(new Task(projectTitle,0,-1,projectStartDate,projectEndDate,"","",projectCompleted,projectDesc));
        var tasksResult = JSON . parse(result);
        //push all project tasks
        for (var i=0; i <tasksResult.tasks.length; i ++){

            tasks.push(new Task(
                tasksResult.tasks[i].title,
                tasksResult.tasks[i].id,
                tasksResult.tasks[i].parentid,
                tasksResult.tasks[i].startdate,
                tasksResult.tasks[i].enddate,
                tasksResult.tasks[i].firstname + " " + tasksResult.tasks[i].lastname,
                tasksResult.tasks[i].usertaskID,
                tasksResult.tasks[i].completed,
                tasksResult.tasks[i].description
            ));
        }
        //set child array
        appendChildrenTasks();
        //create table

        //Empty select box to prevent duplicate
        $("#new-task-parentID").find('option').remove().end();

        $("#tasks-table-body").html("");
        tasks[0].generateRow();
            //fill new form select box


        //bind collapsable button
        bindTableRowClick();
        taskUsersToTable();
    });
}

var taskUserList = [];
function bindTableViewMore(){
    for (var i = 0; i < tasks.length; i ++){
        $("."+tasks[i].id+"-view-more-d").bind("click",(function (i) {
            return function(e){
                e.stopImmediatePropagation();  //http://stackoverflow.com/questions/8019797/jquery-making-entire-table-row-a-link-apart-from-one-column

                $("#tasks-table").stop().slideToggle();
                $("#single-task-view").stop().slideToggle();
                $("#new-task-button").stop().slideToggle();

                currentTask = tasks[i].id;
                if (currentTask !== 0){
                    getTaskUsersFromServer(currentTask);
                } else {
                    $("#task-select-users-remove").append('<option value="">All users.</option>');
                }


                $("#current-task-title").html("<b>"+tasks[i].title+ "&nbsp; | &nbsp;Deadline: " + tasks[i].endDate.toDateString() + "</b>");

                var creatorHTML = "";
                if (tasks[i].creator != ""){
                    creatorHTML = "Task created by: "+tasks[i].creator;
                }
                var status = "";
                if (tasks[i].completed){
                    status = "complete";
                } else {
                    status = "not complete";
                }
                $("#current-task-dates").html("The task is "+status +".<br/>"+creatorHTML+
                    "<br/>Task began on: "+tasks[i].startDate.toDateString());
                $("#current-task-description").html(tasks[i].description);

                if (tasks[i].completed == 1){
                    $("#complete-task-checkbox").prop('checked', true);
                } else {
                    $("#complete-task-checkbox").prop('checked', false);
                }


            }
        })(i));
    }
}

$().ready(function () {
    if (page != "graphs") {
        tasksFromServer();
        $("#new-task-save").bind("click", function () {
            var title = $("#new-task-title").val();
            var description = $("#new-task-description").val();
            var parentID = $("#new-task-parentID").val();
            var startDate = $("#new-task-startdate").val();
            var endDate = $("#new-task-enddate").val();

            var valid = true;
            var errorMsg = "";
            if (title == "") {
                errorMsg += "The title must contain a value.<br/>";
                valid = false;
            }
            if (description == "") {
                errorMsg += "The description must contain a value.<br/>";
                valid = false;
            }
            if (startDate == "") {
                errorMsg += "You must choose a start date.<br/>";
                valid = false;
            }
            if (title == "") {
                errorMsg += "You must choose an end date.<br/>";
                valid = false;
            }

            if (valid) {
                $("#new-task-div").stop().slideToggle();
                $.post("project.php", {
                    action: 'addtask',
                    parent_id: parentID,
                    title: title,
                    desc: description,
                    startDate: startDate,
                    endDate: endDate
                }, function () {
                    tasks = [];
                    tasksFromServer();
                })
            } else {
                $("#new-task-error").html(errorMsg).stop().fadeIn().fadeOut(5000);
            }
        });

        $("#new-task-button").bind("click", function () {
            $("#new-task-div").stop().slideToggle();
        });
        $("#back-to-table").bind("click", function () {
            $("#tasks-table").stop().slideToggle();
            $("#single-task-view").stop().slideToggle();
            $("#new-task-button").stop().slideToggle();
            $("#task-form-mark-complete").stop().slideUp();
        });
        $("#task-control-delete-menu").bind("click", function () {
            $("#task-form-delete").stop().slideToggle();
            $("#task-form-add-user").stop().slideUp();
            $("#task-form-remove-user").stop().slideUp();
            $("#task-form-add-sub-task").stop().slideUp();
            $("#task-form-mark-complete").stop().slideUp();
        });
        $("#task-control-add-user-menu").bind("click", function () {
            $("#task-form-delete").stop().slideUp();
            $("#task-form-add-user").stop().slideToggle();
            $("#task-form-remove-user").stop().slideUp();
            $("#task-form-add-sub-task").stop().slideUp();
            $("#task-form-mark-complete").stop().slideUp();
        });
        $("#task-control-remove-user-menu").bind("click", function () {
            $("#task-form-delete").stop().slideUp();
            $("#task-form-add-user").stop().slideUp();
            $("#task-form-remove-user").stop().slideToggle();
            $("#task-form-add-sub-task").stop().slideUp();
            $("#task-form-mark-complete").stop().slideUp();
        });
        $("#task-control-add-sub-task-menu").bind("click", function () {
            $("#task-form-delete").stop().slideUp();
            $("#task-form-add-user").stop().slideUp();
            $("#task-form-remove-user").stop().slideUp();
            $("#task-form-add-sub-task").stop().slideToggle();
            $("#task-form-mark-complete").stop().slideUp();
        });
        $("#task-control-complete-menu").bind("click", function () {
            $("#task-form-delete").stop().slideUp();
            $("#task-form-add-user").stop().slideUp();
            $("#task-form-remove-user").stop().slideUp();
            $("#task-form-add-sub-task").stop().slideUp();
            $("#task-form-mark-complete").stop().slideToggle();
        });
        $("#new-task-save2").bind("click", function () {
            var title = $("#new-task-title2").val();
            var description = $("#new-task-description2").val();

            console.log(currentTask);
            var parentID = currentTask;
            var startDate = $("#new-task-startdate2").val();
            var endDate = $("#new-task-enddate2").val();
            console.log(parentID);

            var valid = true;
            var errorMsg = "";
            if (title == "") {
                errorMsg += "The title must contain a value.<br/>";
                valid = false;
            }
            if (description == "") {
                errorMsg += "The description must contain a value.<br/>";
                valid = false;
            }
            if (startDate == "") {
                errorMsg += "You must choose a start date.<br/>";
                valid = false;
            }
            if (title == "") {
                errorMsg += "You must choose an end date.<br/>";
                valid = false;
            }

            if (valid) {
                $("#task-form-add-sub-task").stop().slideToggle();
                $.post("project.php", {
                    action: 'addtask',
                    parent_id: parentID,
                    title: title,
                    desc: description,
                    startDate: startDate,
                    endDate: endDate
                }, function () {
                    tasks = [];
                    tasksFromServer();
                })
            } else {
                $("#new-task-error2").html(errorMsg).stop().fadeIn().fadeOut(5000);
            }
        });
        $("#task-control-add-user").bind("click", function () {
            var user = $("#task-select-users-add").val();
            var errMsg = "";
            var valid = true;
            if (currentTask == 0) {
                errMsg += "All users are working on the project.";
                valid = false;
            }
            for (var i = 0; i < taskUserList.length; i++) {
                if (taskUserList[i].email == user) {
                    errMsg += "This user is already working on the task.";
                    valid = false;
                }
            }
            if (valid) {
                $("#task-form-add-user").stop().slideToggle();
                $.post("project.php", {
                    action: 'addtaskuser',
                    taskid: currentTask,
                    userid: user
                }, function () {
                    tasks = [];
                    tasksFromServer();
                    getTaskUsersFromServer(currentTask);
                })
            } else {
                $("#add-user-task-error").html(errMsg).stop().fadeIn().fadeOut(5000);
            }
        });
        $("#task-control-remove-user").bind("click", function () {
            var user = $("#task-select-users-remove").val();
            if (user !== "") {
                $.post("project.php", {
                    action: 'removetaskuser',
                    taskid: currentTask,
                    userid: user
                }, function () {
                    tasks = [];
                    tasksFromServer();
                    getTaskUsersFromServer(currentTask);
                    $("#task-form-remove-user").slideUp();
                })
            } else {
                $("#remove-user-task-error").html("A valid user must be selected.").stop().fadeIn().fadeOut(5000);
            }
        });
        $("#task-control-delete").bind("click", function () {
            if ($('#delete-task-checkbox').is(":checked")) {
                if (currentTask == 0) {
                    $("#delete-task-error").html("You cannot delete the project task.").stop().fadeIn().fadeOut(5000);
                } else {
                    $("#task-form-delete").slideUp();
                    for (var i = 0; i < tasks.length; i++) {
                        if (tasks[i].id == currentTask) {
                            tasks[i].deleteTask()
                        }
                    }
                }
            } else {
                $("#delete-task-error").html("You must confirm by checking the confirmation checkbox.").stop().fadeIn().fadeOut(5000);
            }
        });
        $("#task-control-mark-complete").bind("click", function () {
            $.when(getTaskByID(currentTask).completeTask()).then(tasksFromServer());

        });
        getUsersFromServer();
    }
});

function getUsersFromServer() {
    $.post("project.php", {
        action: 'getuserlist'
    }, function (result) {
        //fill add select
        $("#task-control-add-user").find('option').remove().end();
        result = JSON.parse(result);
        for (var i = 0;i < result.length; i++){
            $("#task-select-users-add").append('<option value="'+ result[i].email +'">'+result[i].firstname +' ' + result[i].lastname+'</option>');
        }
    })
}

function getTaskUsersFromServer(taskID) {
    $.post("project.php", {
        action: 'gettaskusers',
        taskid: taskID
    }, function (result) {
        //set userlist in tasks + add 2 table
        //fill remove select
        $("#task-select-users-remove").find('option').remove().end();
        result = JSON.parse(result);
        if (result.length == 0 ){
            $("#task-select-users-remove").append('<option value="">No users.</option>');
        }
        for (var i = 0;i < result.length; i++) {
            $("#task-select-users-remove").append('<option value="' + result[i].email + '">' + result[i].firstname + ' ' + result[i].lastname + '</option>');
        }
        taskUserList = result;
        var userListHTML = "";
        for (var j=0;j<taskUserList.length;j++){
            if (taskUserList.length !== 1 && j !==0){ //only "/" on 2nd++ and when more than  one user
                userListHTML += " / ";
            }
            userListHTML += taskUserList[j].firstname +" "+taskUserList[j].lastname;
        }
        if (userListHTML == ""){
            userListHTML = "None";
        }
        $("#userlist-"+taskID).html(userListHTML)

    })
}

function taskUsersToTable(){
    for (var i = 0;i<tasks.length;i++){
        if (tasks[i].id !== 0){
            getTaskUsersFromServer(tasks[i].id)
        }
    }
}