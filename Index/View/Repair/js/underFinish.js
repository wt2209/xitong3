/**
 * Created by WT on 2017/8/12.
 */
$(function(){
    $('.print-button').click(function(){
        var id = $(this).attr('id');

        var currentRow = $("#print-"+id);
        var table = $("#table");
        var url = table.attr('print-url');
        var myDate = new Date();
        var currentTime = myDate.getFullYear() + '-' +(myDate.getMonth()+1) + '-' + myDate.getDate()
            + ' '+myDate.getHours() + ':' + myDate.getMinutes();

        table.addClass('table-bordered');
        currentRow.removeClass('no-print');
        table.print({
            //Use Global styles
            globalStyles : true,
            //Add link with attrbute media=print
            mediaPrint : false,
            //Print in a hidden iframe
            iframe : false,
            //Don't print this
            noPrintSelector : ".no-print",
            //Add this at top
            prepend : "<p style='font-size: 16px;font-weight: bold;text-align: center;'>维修申请单</p><br/>",
            //Add this on bottom
            append : "<p style='text-align: right'>打印时间： "+currentTime+"</p> "
        });
        currentRow.addClass('no-print');
        table.removeClass('table-bordered');

        $.post(url, 'id='+id, function(data){}, 'json');

    });


    $('#finishForAll').click(function(){
        var url = $("#finishForAll").attr('url');
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
