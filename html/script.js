$(function() {
    $(".content").hide();
    $("#defaultcontents").show();
    $(".game_link").click(function(a) {
        $(".content").hide();
        $("#"+a.target.id.substring(0, a.target.id.length - 5)+"_contents").show();
    });
})
