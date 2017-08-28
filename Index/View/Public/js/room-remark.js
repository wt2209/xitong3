$(function(){
    $('.room_detail').dblclick(function(){
        var sSpanHtml = $(this).find('span').html();
        $(this).find('span').hide();
        $(this).find('textarea').val(sSpanHtml).removeClass('hide').show().focus()
    })
    $('.room_detail').find('textarea').blur(function(){
        var sHtml = $(this).val();
        var sOldHtml = $(this).parents('.room_detail').find('span').html();
        var sUrl = $(this).attr('_url');
        var self = this;
        $(this).addClass('hide')
        $('.room_detail').find('span').show();
        if (sHtml == sOldHtml) {
            return false;
        }
        hd_ajax(sUrl, $(self).parent().serialize(), function(e){
            $(self).parents('.room_detail').find('span').html(sHtml);
            return false;
        });
    });
})