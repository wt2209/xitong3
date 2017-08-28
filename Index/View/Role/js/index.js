//复选框选后，将子集checked选中
$(function () {
    $('.parent').click(function(){
        if(this.checked){
            $(this).parents('li').find('.child').each(function(){this.checked=true;});
        }else{
            $(this).parents('li').find('.child').each(function(){this.checked=false;});
        }
    })
    $('.child').click(function(){
        var status = false;
        $(this).parents('.level3').find('.child').each(function(){
            if (this.checked) {
                status = true
            }
            if (status) {
                $(this).parents('li').find('.parent').each(function(){this.checked=true;});
            } else {
                $(this).parents('li').find('.parent').each(function(){this.checked=false;});
            }
        })
    })

})