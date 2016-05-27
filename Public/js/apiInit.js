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
    var param = {accuracy:10,filter:1,autoStop:true};
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
    aMap.open();
    aMap.getLocation(param,resultCallback);
}


//设置cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cookie_name){
    var allcookies = document.cookie;
    var cookie_pos = allcookies.indexOf(cookie_name);   //索引的长度

    // 如果找到了索引，就代表cookie存在，
    // 反之，就说明不存在。
    if (cookie_pos != -1)
    {
        // 把cookie_pos放在值的开始，只要给值加1即可。
        cookie_pos += cookie_name.length + 1;      //这里容易出问题，所以请大家参考的时候自己好好研究一下
        var cookie_end = allcookies.indexOf(";", cookie_pos);

        if (cookie_end == -1)
        {
            cookie_end = allcookies.length;
        }
        var value = unescape(allcookies.substring(cookie_pos, cookie_end));         //这里就可以得到你想要的cookie的值了。。。
    }
    return value;
}
//清除cookie
function clearCookie(name) {
    setCookie(name, "", -1);
}

/**
 *读取数据
 */
function getV(k) {
    var r = $api.getStorage(k);
    if (r && typeof r != 'undefind') {
        return r;
    } else {
        return false;
    }
}

/**
 *写入数据
 */
function setV(k, v) {
    $api.setStorage(k, v);
}

/**
 *删除数据
 */
function delV(k) {
    $api.setStorage(k, null);
}



/**
 *发送ajax 更新自己信息
 */
function sendInfo(data){
    $.ajax({
        url:baseUrl+'server/updateInfo',
        data:data
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