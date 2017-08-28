$(function(){
    //验证输入
    $('#addSDBaseSubmit').click(function(){
        var year = parseInt($('#year').val());
        var month = parseInt($('#month').val());

        if (!month || !year) {
            hd_alert({message:'错误：年度和月度必须填写！'})
            return false;
        }
        if (month < 1 || month > 12) {
            hd_alert({message:'错误：请填写一个正确的月份！'})
            return false;
        }
        $('#_mask').show();


    })
})