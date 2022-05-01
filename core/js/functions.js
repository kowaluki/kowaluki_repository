function bind(start) {
    $("*").off();
    if(start) {
        let app = location.href;
        app = app.split("/");
        app[5] ? createConstants(true): createConstants();
    }
    else {
        createEvents();
    }
}

function createConstants(app) {
    let url = location.href;
    url = url.split("/")[5];
    if(url==="app") {
        $("html").attr("lang","en-EN");
        $("head").append("<meta charset='UTF-8' />");
        $("head").append("<title></title>");
        $("head").append("<link rel='stylesheet' type='text/css' href='./website/app.css' />");
        $("title").text("Articles");
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
    $("body").prepend("<main></main>");
    $("body").prepend("<header></header>");
    $("header").load("./elements/header");
    //Load Articles
    loadArticles();
    bind();
    //The ability to log in
}

function loadArticles() {
    $.ajax({
        url: "./api/loadArticles",
        success: function(response) {
            console.log(response);
        }
    })
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
            data: {

            },
            
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