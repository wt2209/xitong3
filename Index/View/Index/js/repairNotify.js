/**
 * Created by WT on 2017/8/30.
 */

var queryNotifyData = function(){
    var unreviewed = $('#unreviewed');
    var url = unreviewed.attr('url');
    $.get(url, function(response) {

        if (response.status == 1) {
            var unreviewedCount = response.data.unreviewedCount;

            if (unreviewedCount > 0) {
                unreviewed.text(unreviewedCount);
                unreviewed.show();
            } else {
                unreviewed.hide();
            }
        }
    }, 'json')
}

queryNotifyData();
window.queryNotifyData = queryNotifyData

