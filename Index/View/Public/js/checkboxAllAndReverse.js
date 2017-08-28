/**
 * Created by WT on 2015/4/29.
 */
$(function(){

    $(".selectAll").click(function(){
        if(this.checked){
            $(this).parent().siblings('form').find('input[type=checkbox]').each(function(){this.checked=true;});
        }else{
            $(this).parent().siblings('form').find('input[type=checkbox]').each(function(){this.checked=false;});
        }
    });
    $('.reverse').click(function(){
            $(this).parent().siblings('form').find('input[type=checkbox]').each(function(){this.checked=!this.checked;});
    })

})
