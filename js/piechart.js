/**
 * Created by Andy on 30/01/2017.
 */
var userTaskCount = [];
var users = [];
var dataSet = [];
var dataFromServer = false;
function initPie(){
    //Get all project users.
    $.post("project.php",{action:'getuserlist'},function (result) {
        users = JSON.parse(result);
        for (var x=0 ; x<users.length; x++){ //Init count array for each user
            userTaskCount.push(0);
        }
        for (var i = 0; i < tasks.length; i++){
            //Get all users for each task
            if (tasks[i].id != 0){
                $.post("project.php", {
                    action: 'gettaskusers',
                    taskid: tasks[i].id
                }, function (result) {
                    result = JSON.parse(result);
                    for (var j = 0; j<result.length; j++){
                        for (var k = 0;k<users.length;k++){
                            if (result[j].email == users[k].email){
                                userTaskCount[k]++;
                            }
                        }
                    }
                    dataFromServer = true;
                });
            }
        }
    });
}

function pieWait(){
    if (!dataFromServer){
        setTimeout(pieWait,200)
    } else {
        for (var y=0;y<users.length;y++){
            if (userTaskCount[y]!==0){
                dataSet.push({user:users[y].firstname + " " +users[y].lastname,count:userTaskCount[y]});
            }
        }
        // http://bl.ocks.org/hunzy/9134534
        console.log(dataSet); // Data is ready to be used.
        var width = $("#body").width() - 50;
        var height = 500;
        var radius = Math.min(width, height) / 2;
        var color = d3.scale.category20c();
        var svg = d3.select("#workload-chart").insert("svg",":first-child")
            .attr("width", width)
            .attr("height", height)
            .append("g")
            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        var arc = d3.svg.arc()
            .outerRadius(radius)
            .innerRadius(0);

        var pie = d3.layout.pie()
            .sort(null)
            .value(function(d){ return d.count; });

        var g = svg.selectAll(".fan")
            .data(pie(dataSet))
            .enter()
            .append("g")
            .attr("class", "fan");

        g.append("path")
            .attr("d", arc)
            .attr("fill", function(d){ return color(d.data.user) })
            .attr("data-legend", function(d){return d.data.user + " - " + d.data.count});

        g.append("text")
            .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
            .style("text-anchor", "middle")
            .text(function(d) { console.log(d);return d.data.user });

        var legend = svg.append("g")
            .attr("class", "legend")
            .attr("transform", "translate(-"+width/2.1+",0)");

        setTimeout(function() {
            legend
                .style("font-size","16px")
                .call(d3.legend)
        },10)
    }
}