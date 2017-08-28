$(function(){
    // 控制房间浮动 偶数清楚浮动
    var room = $('.room');
    for (var i = 0; i < room.length; i++) {
        if(i % 2==0)
            room.eq(i).addClass('clear');
    };
    var person = $('.room').find('.person');
    for (var i = 0; i < person.length; i++) {
        //最小高度
        if(person.eq(i).find('.remark').height() < 36){
            person.eq(i).find('.remark').css('height','36px');
        }
        if(i % 2==0)
            person.eq(i).addClass('clear');
    };




})