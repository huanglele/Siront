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


//设置cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
//清除cookie
function clearCookie(name) {
    setCookie(name, "", -1);
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

/**
 * 申请一个任务
 */
function sureTask(tid){
    mui.toast('我在html');
    $.ajax({
        url:baseUrl+'server/sureTask',
        data:{
            tid:tid
        },
        dataType:'json',
        success:function(data){
            mui.toast(data.msg);
            if(data.status){
                window.location.href = data.url;
            }
        }
    })
}