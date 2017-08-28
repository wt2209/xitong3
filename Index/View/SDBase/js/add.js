$(function(){
    //添加行
    $('#addRows').click(function(){
        var iRowNumber = parseInt($('#rowNumber').val()) ? parseInt($('#rowNumber').val()) : 10;
        var iCurrentNumber = parseInt($('.table tr:last-child').attr('_num'));
        if (!iCurrentNumber) {
            return false;
        }
        var oTable= $('.table');
        var iTotalNumber = iRowNumber + iCurrentNumber
        for (var i = iCurrentNumber+1; i <= iTotalNumber; i++) {
            var sTr = '<tr _num="'+ i +'">';
            sTr += '<td><input type="text" class="form-control" name="'+ i +'[room]" placeholder="房间号"/></td>';
            sTr += '<td><input type="text" class="form-control"  name="'+ i +'[electric_base]" placeholder="电表底数"/></td>';
            sTr += '<td><input type="text" class="form-control" name="'+ i +'[water_base]" placeholder="水表底数"/></td>';
            sTr += '<td><input type="text" class="form-control"  name="'+ i +'[name]" placeholder="抄表人"/></td>';
            sTr += '<td><input type="text" class="form-control" name="'+ i +'[read_time]" placeholder="抄表时间"/></td>;'
            sTr += '<td><input type="text" class="form-control" name="'+ i +'[remark]" placeholder="备注"/></td>';
            sTr += '<td></td></tr>';
            oTable.append(sTr);
        }
    })

    $('#addSDBaseSubmit').click(function(){
        var sTotalName = $('#total_name').val() ? $('#total_name').val() : '';
        var sTotalReadTime = $('#total_read_time').val() ? $('#total_read_time').val() : '';
        var year = parseInt($('#year').val());
        var month = parseInt($('#month').val());
        var sUrl = $("#addSDBase").attr('_url');

        //验证输入
        if (!month || !year) {
            hd_alert({message:'错误：年度和月度必须填写！'})
            return false;
        }
        if (month < 1 || month > 12) {
            hd_alert({message:'错误：请填写一个正确的月份！'})
            return false;
        }
        $('#_mask').show();

        var data = $("#addSDBase").serialize() + '&year=' + year + '&month=' + month +'&totalName=' + sTotalName + '&totalReadTime=' + sTotalReadTime;
        //发送ajax请求
       $.post(sUrl, data, function(data){

           $('.content').html(data)
           $('#_mask').hide();
       }, 'html');

        return false;
    })
})