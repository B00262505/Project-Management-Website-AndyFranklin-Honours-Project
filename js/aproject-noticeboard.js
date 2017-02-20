/**
 * Created by Andy on 30/10/2016.
 */
var message = "";

$().ready(function () {
    setProjectVariables();
    setNoticeboardHTML();
    $("#new-noticeboard-clear").bind("click",function () {
        clearForm();
    });

    $("#new-noticeboard-message-button").bind("click",function () {
        
        $("#new-noticeboard-post").stop().slideToggle();
    });
    $('#new-noticeboard-message-file-input').change(function () {
        var fullPath = $(this).val();
        var filename = fullPath.replace(/^.*[\\\/]/, '');
        $("#new-noticeboard-message-file-text").val(filename);
    });
    $("#new-noticeboard-messsage-submit").bind("click",function(){
        var formData = new FormData($('form')[0]);
        // for (var [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }
        var errorMessage ="";
        var valid = true;
        if ($("#new-noticeboard-message-title").val() == ""){
            errorMessage += "The notice requires a title.<br/>";
            valid = false;
        }
        if ($("#new-noticeboard-message-description").val()==""){
            errorMessage += "The notice requires a message body.<br/>";
            valid = false;
        }
        if (valid){
            $.ajax({
                url: 'message.php',
                type: 'POST',
                success: function (result) {
                    if (result == "success"){
                        clearForm();
                        setNoticeboardHTML();
                    } else {
                        $("#new-noticeboard-message-error").html(result).stop().fadeIn(10).fadeOut(5000);
                    }
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            $("#new-noticeboard-message-error").html(errorMessage).stop().fadeIn(10).fadeOut(5000);
        }
    });
});

function clearForm(){
    $("#new-noticeboard-post").stop().slideUp();
    $("#new-noticeboard-message-title").val("");
    $("#new-noticeboard-message-file-text").val("");
    $("#new-noticeboard-message-description").val("");

}

function setNoticeboardHTML(){
    $.post("message.php",{action:"getprojectmessages"},function(result){
        message = JSON.parse(result);
        var messagesHTML = "";
        if (message.length == 0) {
            messagesHTML += generateMessageHTML(message);
        }
        for (var i = 0; i<message.length;i++) {
            messagesHTML += generateMessageHTML(message[i]);
        }

        $("#noticeboard-message-container").html(messagesHTML);

        var messageCount = "messageCount-"+projectID;
        localStorage.setItem(messageCount, message.length);
    });
}

function generateMessageHTML(message){
    var htmlMessage ="";

    htmlMessage+="<div class='panel panel-default'>" +
        "<div class='panel-heading blue-bg'>";

    if ($.isEmptyObject(message)){
        htmlMessage+="<h3 class='panel-title'><b>Nothing to see here.</b></h3></div>" +
            "<div class='panel-body'><div class='row'><h3 class='noticeboard-title'>Why not be the first to post a message to the noticeboard?</h3></div><div class='row'>";
        htmlMessage += "<div class='col-md-4'>" +
            "<a href='images/First-Post2.jpg' data-lightbox='I can tell you&apos;re pumped.'>" +
            "<img rel='lightbox' src='images/First-Post2.jpg' class='img-responsive'/></a></div>" +
            "<div class='col-md-8'><br/>Be the first post of many. <br/>Go on.... You know you want to.</div></div>";
    } else {
        htmlMessage+="<h3 class='panel-title'><b>"+ message.firstname+" "+message.lastname+ "&nbsp;&nbsp; | &nbsp;&nbsp;"+message.timeDate+"</b></h3></div>" +
            "<div class='panel-body'><div class='row'><h3 class='noticeboard-title'>" + message.title +"</h3></div><div class='row'>";
        if (message.imageID !== null){
            htmlMessage += "<div class='col-md-4'>" +
                "<a href='uploads/" + projectID +"/"+ message.name+ "' data-title='"+ message.name +"' data-lightbox='"+ message.name+"-"+projectID+ "'>" +
                "<img rel='lightbox' src='uploads/" + projectID +"/"+ message.name+ "' class='img-responsive'/></a></div>" +
                "<div class='col-md-8'>"+ message.body+"</div></div>";
        } else {
            htmlMessage += "<div class='col-md-12'>"+ message.body+"</div></div>"
        }
    }




    htmlMessage+="</div></div>";
    return htmlMessage;
}

