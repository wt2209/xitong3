function initModal() {
    var html = '<div class="modal-dialog">'+
        '<div class="modal-content">'+
        '<div class="modal-header">'+
        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
        '<h4>正在加载...</h4>' +
        '</div>'+
        '<div class="modal-body">'+
        '<span class="modalLoadingIco"></span>'+
        '</div>'+
        '<div class="modal-footer">'+
        '</div>'+
        '</div>'+
        '</div>';
    $('#MyModal').html(html)
    $('#MyModal').modal('show');
}


$(function(){
    //退房
    $('.quitButton').click(function(){
        var oInput = $('#quitForm').find('input[name=id]');
        //清空原数据
        oInput.val('');
        var id = $(this).attr('_id');
        oInput.val(id);
        $('#quitModal').modal('show')
    })
    //删除
    $('.delButton').click(function(){
        var oInput = $('#delForm').find('input[name=id]');
        //清空原数据
        oInput.val('');
        var id = $(this).attr('_id');
        oInput.val(id);
        $('#delModal').modal('show')
    })

    //租赁入住
    // TODO*/


    // 显示模态框
    $('.modalShow').click(function () {
        initModal();
        var url = $(this).attr('_url');
        if (!url) {
            $('#MyModal').on('shown.bs.modal', function (e) {
                $('#MyModal').modal('hide');
            })
            hd_alert({
                message:'参数错误！'
            })
        } else {
            $.ajax({
                url:url,
                type:'get',
                dataType:'json',
                success:function(e){
                    if (e.status) {
                        $('#MyModal').html(e.message);
                    } else {
                        var sError = '<span class="modalLoadingError">'+ e.message + '</span>';
                        $('.modal-body').html(sError);
                        $('.modal-header').find('h4').html('加载错误');
                    }
                }
            })
        }
    })


})