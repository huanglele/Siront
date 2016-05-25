/**
 * Created by Sun on 2016/5/23.
 */
baseUrl = 'http://xyc.91yiso.com/mobile.php/';
jpush = null;
jpushDeviceId = null;

aMapLocation = null;


apiready = function(){
    initJPush();

    aMapLocation = api.require('aMapLocation');
}



/**
 * 初始化极光推送
 */
function initJPush() {
    jpush = api.require('ajpush');
    jpush.init();
    //获取极光推送的设备id
    jpush.getRegistrationId(function(ret) {
        var jpushDeviceId = ret.id;
    });
}

/**
 * 更新自己的信息 位置、设备id
 */
function updateInfo(){
    var param = {accuracy:100,filter:1,autoStop:true};
    var resultCallback = function(ret, err){
        if(ret.status){
            $.ajax({
                url:baseUrl+'server/updateInfo',
                data:{
                    lon:ret.longitude,
                    lat:ret.latitude,
                    deviceId:jpushDeviceId,
                    time:ret.timestamp
                },
                success:function(data){
                    alert(date);
                }
            })
            //alert("经度：" + ret.longitude +"\n纬度："+ ret.latitude + "\n时间：" + ret.timestamp);
        } else {
            //alert(err.code + ',' + err.msg);
        }
    }
    aMapLocation.startLocation(param,resultCallback);
}
