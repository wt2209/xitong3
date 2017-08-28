/**
 * Created by WT on 2015/5/8.
 */

$(function(){
    $('.college').click(function(){
        var sUrl = $(this).attr('_url');
        //删除当前的iframe
        $("#task-content-inner li[app-id='College']", parent.document).remove();
        parent.window.openapp(sUrl, 'College','大学生');
    })
    $('.rent').click(function(){
        var sUrl = $(this).attr('_url');
        //删除当前的iframe
        $("#task-content-inner li[app-id='RentList']", parent.document).remove();
        parent.window.openapp(sUrl, 'RentList','租赁住户');
    })
    $('.rent_h').click(function(){
        var sUrl = $(this).attr('_url');
        //删除当前的iframe
        $("#task-content-inner li[app-id='RentList']", parent.document).remove();
        parent.window.openapp(sUrl, 'RentList','租赁住户');
    })
    $('.worker_h').click(function(){
        var sUrl = $(this).attr('_url');
        //删除当前的iframe
        $("#task-content-inner li[app-id='Worker']", parent.document).remove();
        parent.window.openapp(sUrl, 'Worker','职工');
    })
    $('.dispatch').click(function(){
        var sUrl = $(this).attr('_url');
        //删除当前的iframe
        $("#task-content-inner li[app-id='DispatchList']", parent.document).remove();
        parent.window.openapp(sUrl, 'DispatchList','派遣工');
    })
})

