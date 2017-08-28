$(function(){
    $('#formSubmit').click(function(){
        var iStartYear = parseInt($('#startYear').val());
        var iEndYear = parseInt($('#endYear').val());
        var iStartMonth = parseInt($('#startMonth').val());
        var iEndMonth = parseInt($('#endMonth').val());

        var sUrl = $('#createForm').attr('_url');

        //验证输入
        if (!iStartYear || !iEndYear || !iStartMonth || !iEndMonth) {
            hd_alert({message:'错误：开始年月度和结束年月度必须填写！'})
            return false;
        }
        if (iStartMonth < 1 || iStartMonth > 12 || iEndMonth < 1 || iEndMonth > 12) {
            hd_alert({message:'错误：请填写一个正确的月份！'})
            return false;
        }
        if ((iStartMonth + 100*iStartYear) > (iEndMonth +100 * iEndYear)) {
            hd_alert({message:'错误：开始年月度必须小于结束年月度！'})
            return false;
        }
        $('#_mask').show();

        $.post(sUrl, $('#createForm').serialize(), function(data){
            $('.content').html(data)
            $('#_mask').hide();
        }, 'html');

        return false;
    })
})
