/**
 * Created by WT on 15-4-13.
 */
$(function (){
    var iWinHeight = $(window).height();
    var iPageTop = (iWinHeight - $('.page').height()) / 2 + 20;
    var iBuildingTop = (iWinHeight - $('.building').height()) / 2 + 20;
    $('.page').css({
        top:iPageTop
    }).show()
    $('.building').css({
        top:iBuildingTop
    }).show()
})