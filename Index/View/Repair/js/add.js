/**
 * Created by WT on 2017/8/12.
 */

$(function(){
    $('#submit').click(function(){
        var sUrl = $("#form").attr('_url');
        $('#_mask').show();
        //发送ajax请求
        $.post(sUrl, $("#form").serialize(), function(data){
            $('#_mask').hide();
            hd_alert({message:data.message});
            if (data.status == 1) {
                $("#reset").trigger('click');
            }
        }, 'json');
        return false;
    })
})
