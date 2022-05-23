let articles;
function bind(start) {
    $("*").off();
    if(location.href.split("/")[6]=="author") {
        author();
    }
    else if(location.href.split("/")[6]=="article") {
        article(location.href.split("/")[7]);
    }
    else if(start) {
        let app = location.href;
        app = app.split("/");
        app[6] ? createConstants(true): createConstants();
    }
    else {
        createEvents();
    }
}
function article(id) {
    $.ajax({
        url: "../api/showArticle/",
        data: {
            id: id
        },
        success: function(response){
            if(response.info && response.info=="only for logged") {
                alert("log in to view the article");
                $("body").html("Go back to <a href='../'>home page</a>.")
            }
            else {
                response = response[0];
                $("body").append('<h2>'+response.title+'</h2>');
                $("body").prepend('<div id="date">'+response.date+'</div>');
                $("head title").text(response.title);
                $("body").append('<p>Author: <a href="../author/'+response.author+'" title="'+response.author+' - article\'s author" target="_blank"><i>'+response.author+'</i></a></p>');
                $("body").append('<div id="article">'+response.article+'</div>');
            }
        }
    });
}
function checkloging() {
    $.ajax({
        url: "./api/checkLoging",
        success: function(response) {
            if(response) {
                $("#login").hide();
                $("#logout").show();
                loadArticles();
            }
        }
    });
}
function createConstants(app) {
    let url = location.href;
    url = url.split("/")[6];
    if(url==="app") {
        $("html").attr("lang","en-EN");
        $("head").append("<meta charset='UTF-8' />");
        $("head").append("<title></title>");
        $("head").append("<link rel='stylesheet' type='text/css' href='./website/app.css' />");
        $("title").text("Articles");
        checkloging();
    }
    //Unselected body 
    $("body").attr("unselectable", "on").on("selectstart dragstart", false);
    //dungeon
    if(app) {
        $("body").prepend("<div id='dungeon'></div>");
        $("#dungeon").append("<div id='logreg'></div>");
        $("#dungeon").append("<div id='x'>X</div>");
        $("#logreg").load("./elements/logreg");
    }
    //C&L: header, main and footer
    $("body").prepend("<footer></footer>");
    $("footer").load("./elements/footer");
    $("body").prepend("<div id='login'>Log in</div>");
    $("body").prepend("<div id='logout'>Log out</div>");
    $("body").prepend("<main></main>");
    $("body").prepend("<header></header>");
    $("header").load("./elements/header");
    //Load Articles
    bind();
    //The ability to log in
}
function loadArticles() {
    $.ajax({
        url: "./api/loadArticles",
        success: function(response) {
            articles = response;
            showArticles();
        }
    });
}
function showArticles() {
    $("#x").click();
    let number = 0;
    let timeOpen = 700;
    let timeDelay = 0;
    $.each(articles,function(){
        $("main").append('<div class="article" id="article_'+number+'"></div>');
        $("#article_"+number).append('<p class="title">Title: <strong>'+this.title+'</strong></p>');
        if(typeof this.date.$date !== "undefined") {
            let $date = longToDate(this.date.$date.$numberLong);
            $("#article_"+number).append('<div class="date">'+$date.dotsDMY+'</div>');
        }
        else {
            $("#article_"+number).append('<div class="date">'+this.date+'</div>');
        }
        $("#article_"+number).append('<div class="author">Author: <strong><a href="./author/'+this.author+'" title="'+this.author+' - article\'s author" target="_blank">'+this.author+'</a></strong></div>');
        $("#article_"+number).append('<div class="content"><a href="./article/'+this.id+'" target="_blank">Show article...</a></div>');
        
        $(".article").css("opacity","0");
        number++;
    });
    let length = (timeOpen / $(".article").length);
    $($(".article").get()).each(function(){
        $(this).stop().delay(timeDelay).animate({
            "opacity":1
        },180);
        console.log(timeDelay);
        timeDelay += length;
    });
    
    $('html').on('click', function() {
        $(".article").removeClass('chosen');
    });
    $(".article").on("click", function(e){
        e.stopPropagation(); //Nie przekazuje funkcji od rodzica
        $(".article").removeClass("chosen");
        $(this).addClass("chosen");
    });
    $(".article").on("dblclick",function(e){
        e.stopPropagation();
        let link = $(this).find(".content").find("a").attr("href");
        window.open(link);
    });
    $("#logout").click(function() {
        $.ajax({
            url: "./api/logout"
        });
        let timeSet = 700;
        timeDelay = 0;
        length = timeSet / $(".article").length;
        $($(".article").get().reverse()).each(function(){
            $(this).stop().delay(timeDelay).fadeOut(180);
            timeDelay += length;
        });
        setTimeout(() => {
            $("main").text("");
            $("#logout").hide();
            $("#login").show();
            articles = "";
            bind();
            if(alert("Safety log out!")) {
                console.log("aha");
            }
        }, timeSet+180);
    });
}
function createEvents() {
    $("#x").click(function(){
        $("input").text("");
        $("#logreg, #dungeon").stop().fadeOut();
        bind();
    });
    $("#login").click(function(){
        $("#dungeon, #logreg").stop().fadeIn();
        $("#dungeon").css("display", "flex");
    });
    setTimeout(() => {
        $("form").attr("onsubmit","return false");
        $("form").submit(function(json) {
            submit(json, $(this).attr("id"));
        });
    },100);
}
function submit(json,id) {
    switch(id) {
        case "logs":
            loging();
        break;
        case "regs":
            reging();
        break;
    }
}
function loging(is) {
    if(is) {
        $.ajax({
            url: "./api/login",
            method: "POST",
            data: {
                login: $("#loginInput").val(),
                pass: $("#passInput").val()
            },
            success: function(request) {
                alert(request.login+"!");
                $("#loginInput, #passInput").val("");
                if(request.login=="success") {
                    $("#login").hide();
                    $("#logout").show();
                    loadArticles();
                }
            }
        });
    }
    else {
        $.ajax({
            url: "./api/prelogin",
            success: function(data) {
                if(data) {
                    loging(true);
                }
            }
        });
    }
}
function author() {
    let author = location.href.split("/")[7];
    checkAuthor(author);
}
function checkAuthor(author) {
    $.ajax({
        url: "../api/checkAuthor/",
        method: "GET",
        data: {
            author: author
        },
        success: function(response) {
            if(response.info=="only for logged") {
                $("head").append("<title>Log in</title>");
                $("body").append("<h2>Log in</h2>");
            }
            else {
                if(response.length>0) {
                    $("head").append("<title>"+author+"'s articles</title>");
                    $("body").append("<h3>Author:</h3><h1>"+author+"</h1>");
                    $("body").append("<h4>Number of articles: "+response.length+"</h4>");
                    $("body").append("<ul></ul>");
                    $.ajax({
                        url: "../api/showPersonalArticles/",
                        method: "GET",
                        data: {
                            personal: author
                        },
                        success: function(response) {
                            console.log(response);
                            $.each(response, function(){
                                $("ul").append("<li><a href='../article/"+this.id+"' target='_blank'>"+this.title+"</a></li>");
                            });
                        }
                    });
                }
                else {
                    $("head").append("<title>No user</title>");
                    $("body").append("<h2>There is no such user</h2>");
                }
            }
        }
    });
}
function longToDate(long) {
    let $date = new Date(parseInt(long));
    let year = $date.getFullYear().toString();
    let month = $date.getMonth() - 1;
    month<10 ? month = "0"+month: month.toString();
    let day = $date.getDate();
    day<10 ? day = "0"+day: day = day.toString();
    let hours = $date.getHours();
    hours<10 ? hours = "0"+hours: hours = hours.toString();
    let minutes = $date.getMinutes();
    minutes<10 ? minutes = "0"+minutes: minutes = minutes.toString();
    seconds = $date.getSeconds();
    seconds<10 ? seconds = "0"+seconds: seconds = seconds.toString();
    let timeZone = $date.getTimezoneOffset().toString();
    let response = {
        "year":year,
        "month":month,
        "day":day,
        "hours":hours,
        "minutes":minutes,
        "seconds":seconds,
        "timeZone":timeZone,
        "dotsDMY": day+"."+month+"."+year,
        "dotsYMD": year+"."+month+"."+day,
        "dashDMY": day+"-"+month+"-"+year,
        "dashYMD": year+"-"+month+"-"+day,
        "shortTime": hours+":"+minutes,
        "fullTime": hours+":"+minutes+":"+seconds
    };
    return response;
}