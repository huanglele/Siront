/**
 * Created by Sun on 2016/5/23.
 */
baseUrl = 'http://xyc.91yiso.com/mobile.php/';
jpush = null;
jpushDeviceId = null;

aMapLocation = null;


/**
 * 初始化极光推送
 */
function initJPush() {
    jpush = api.require('ajpush');
    jpush.init();
    //获取极光推送的设备id
    jpush.getRegistrationId(function(ret) {
        jpushDeviceId = ret.id;
    });
}

function clearCache(){
    api.clearCache(function(){
        api.toast({
            msg:'清除完成'
        });
    });
}

/**
 * 更新自己的信息 位置、设备id
 */
function updateInfo(){
    var param = {accuracy:100,filter:1,autoStop:true};
    var resultCallback = function(ret, err){
        if(ret.status){
            data = {
                lon:ret.longitude,
                    lat:ret.latitude,
                    deviceId:jpushDeviceId,
                    time:ret.timestamp
            };
            sendInfo(data)
            //alert("经度：" + ret.longitude +"\n纬度："+ ret.latitude + "\n时间：" + ret.timestamp);
        } else {
            //alert(err.code + ',' + err.msg);
            //采用系统定位
            api.startLocation({
                accuracy: '10m',
                filter: 1,
                autoStop: true
            }, function(ret, err){
                if( ret ){
                    alert('采用系统定位');
                    data = {
                        lon:ret.longitude,
                        lat:ret.latitude,
                        deviceId:jpushDeviceId,
                        time:ret.timestamp
                    };
                    sendInfo(data)
                }else{
                    //定位失败
                }
            });
        }
    }
    aMapLocation.startLocation(param,resultCallback);
}


/**
 *发送ajax 更新自己信息
 */
function sendInfo(data){
    $.ajax({
        url:baseUrl+'server/updateInfo',
        data:data,
        success:function(data){
            alert(data);
        }
    })
}