$(function() {

    var newHash      = "",
        $mainContent = $("#main-content"),
        $pageWrap    = $("#page"),
        $menuAnchor  = $("#menu"),
        baseHeight   = 0,
        $el;

    $pageWrap.height($pageWrap.height());
    baseHeight = $pageWrap.height() - $mainContent.height();

    $("nav").delegate("a", "click", function() {
        window.location.hash = $(this).attr("href");
        return false;
    });

    $(window).bind('hashchange', function(){

        newHash = window.location.hash.substring(1);

        if (newHash) {
            $menuAnchor.slideto({
                slide_duration: "slow",
                highlight: false
            });               
            $mainContent
                .find("#content_html")
                //.fadeOut(200);
                .fadeOut(0, function() {
                    $mainContent.hide().load(newHash + " #content_html", function() {
                        $mainContent.fadeIn(0);
                        /*$mainContent.fadeIn(200, function() {
                            $menuAnchor.slideto({
                                highlight: false
                            });
                        });*/
                    });
                $("nav li").removeClass("active");
                $("nav li").addClass("inactive");
                $("nav a[href='"+newHash+"']").parent().addClass("active");
                $("nav a[href='"+newHash+"']").parent().removeClass("inactive");
                $("body").removeAttr("id");
                $("body").removeAttr("class");
                /*$("div#page").removeAttr("style");*/
                });
        } else {
            $("nav li").removeClass("active");
            $("nav li").removeClass("inactive");
            $("div#page").removeAttr("style");
            $("body").addClass("home");
            $(".content").detach();
        };
    });

/*$menuAnchor.slideto({
    highlight: false
});*/

$(window).trigger('hashchange');

});

// ToDo:
// Fix HTML, maybe with http://api.jquery.com/replaceWith/

//via:
// http://css-tricks.com/learning-jquery-fading-menu-replacing-content/
// http://css-tricks.com/dynamic-page-replacing-content/