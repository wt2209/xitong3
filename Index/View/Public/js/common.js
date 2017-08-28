/**
 * Created by WT on 2015/4/19.
 */
//通用效果
$(function(){
    $loading = $(window.parent.loading)
    //点击后显示主窗口中的“正在加载”字样
    $('.building').find('a').click(function(){
        $loading.show();
    })
    $('.page').find('a').click(function(){
        if(!$(this).hasClass('btn-success')){
            $loading.show();
        }
    })



    //取消点击按钮时出现的虚线框
    $('a').focus(function(){
        $(this).blur();
    })
    $('button').focus(function(){
        $(this).blur();
    })

    $('input[type=text]').focus(function(){
        $(this).select();
    })
    $('input[type=checkbox]').focus(function(){
        $(this).blur();
    })

})

