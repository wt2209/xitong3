/**
 * Created by WT on 2017/8/12.
 */
$(function(){
    $('#reviewForAll').click(function(){
        var url = $("#reviewForAll").attr('url');
        $('#_mask').show();
        //发送ajax请求
        $.post(url, '', function(data){
            $('#_mask').hide();
            hd_alert({message:data.message,timeout:0.5, success: function(){
                location.reload()
            }});
        }, 'json');
        return false;
    })
})
