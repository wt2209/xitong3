$(function(){
    //全选
    $(".selectAll").click(function(){
        if(this.checked){
            $('.onChecked').find('input[type=checkbox]').each(function(){this.checked=true;});
        }else{
            $('.onChecked').find('input[type=checkbox]').each(function(){this.checked=false;});
        }
    });

    //批量删除
    $('#batchDel').click(function(){
        $('#delModal').modal('show');
    })
    $('#batchCharge').click(function(){
        $('#chargeModal').modal('show');
    })
})
