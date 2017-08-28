/**
 * Created by WT on 2017/8/12.
 */
$(function(){
    $('#submit').click(function(){
        var url = $("#form").attr('url');
        var callbackUrl = $("#form").attr('callback-url');
        $('#_mask').show();
        //发送ajax请求
        $.post(url, $("#form").serialize(), function(data){
            $('#_mask').hide();
            hd_alert({message:data.message,timeout:0.5, success: function(){
                window.location.href = callbackUrl;
            }});
        }, 'json');
        return false;
    })
})
