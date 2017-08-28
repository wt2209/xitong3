var content =   "<p style='color:#555'>可输入姓名、电话、房间号</p>";
$('#searchInput').focus(function(){
    $(this).popover({
        html:true,
        content:content,
        placement:'bottom'
    })
    $(this).select();
})
$('#searchInput').blur(function(){
    $(this).popover('hide')
})
$('.searchInput').focus(function(){
    $(this).popover({
        html:true,
        content:content,
        placement:'bottom'
    })
    $(this).select();
})
$('.searchInput').blur(function(){
    $(this).popover('hide')
})




$('.singleSearch').click(function(){
    var sValue = $('#searchInput').val();
    if (!sValue) {
        return false;
    } else { //显示“正在加载”
        $(window.parent.loading).show();
    }
})
$('.rentSearch').click(function(){
    $(window.parent.loading).show();
})


