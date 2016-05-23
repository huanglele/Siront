/**
 * Created by Sun on 2016/5/23.
 */

function getDeviceId (){

}

function initJPush() {
    jpush.init(function(ret, err) {
        if (ret && ret.status) {
            alert('操作成功!');
            jpush.getRegistrationId(function(ret) {
                alert(ret.id);
                sjpushDeviceId = ret.id;
                api.ajax({
                    url : baseUrl + '/api/serverinit',
                    data : {
                        values : {
                            jpushDeviceId : sjpushDeviceId
                        }
                    }
                }, function(ret, err) {
                    alert(ret);
                });
            });
        } else {
            alert('操作失败!');
        }
    });
}

function apiInit(){
    api.addEventListener({name:'appintent'}, function(ret,err) {
        alert('通知被点击，收到数据：\n' + JSON.stringify(ret));//监听通知被点击后收到的数据
    });
    api.addEventListener({name:'pause'}, function(ret,err) {
        onPause();//监听应用进入后台，通知jpush暂停事件
    });
    api.addEventListener({name:'resume'}, function(ret,err) {
        onResume();//监听应用恢复到前台，通知jpush恢复事件
    })
}