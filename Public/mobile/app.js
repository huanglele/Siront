/**
 * Created by huanglele on 2016/5/9.
 */

$(window).ready(function(){
    //个人头像点击事件
    $('#btnMenu').click(function(){
        log('发送点击');
        if(isLogin){
            //已经登录显示侧边菜单
            $('#menu').animate({
                'left':0
            });
        }else{
            //没有登录，弹出登录界面
            bgShow('show');
            $('#loginBox').css({
                'display':'block',
                'top':($(window).height()-300)/2
            }).removeClass('hidePopup').addClass('showPopup');
        }
    })

    //关闭菜单按钮
    $('.closeMenu').click(function(){
        $('#menu').animate({
            'left':'-100%'
        });
    })

    //关闭登录界面
    $('#closeLogin').click(function(){
        log('关闭登录界面');
        $('#loginBox').css({
            'display':'none',
            'top':($(window).height()-300)/2
        }).removeClass('showPopup').addClass('hidePopup');
        bgShow('hidden');
    })

    //登录时输入框获取焦点下边框变色
    $('.input-val').each(function(){
        $(this).focus(function(){
            $(this).parent('div').addClass('active');
        }).blur(function(){
            $(this).parent('div').removeClass('active');
        }).on('input',function(){
            checkLoginInput();
        })
    })

    //登录
    $('#login_sub').click(function(){
        log('我被点击了');
        if(checkLoginInput()){
            var dom = $('#loginBox');
            var phone = $('input[name="phone"]',dom);
            var password = $('input[name="password"]',dom);
            $.ajax({
                'url':baseUrl+'/ajax/ajaxLogin',
                'data':{
                    phone:phone.val(),
                    'password':password.val()
                },
                'type':'POST',
                'success':function(data){
                    if(typeof data.msg != 'undefined'){
                        $('#login_tip').html(data.msg);
                    }
                    if(data.status=='success'){
                        isLogin = true;
                        $('.icon-user',$('#menu')).html(data.user);
                        $('#closeLogin').click();
                    }
                }
            })
        }
    })

    map = new aMap();
    map.setContainer('AMap');
    map.init();
    map.findPositionByBrowser();


    /**
     * 选择工作地点
     */
    $('input[name="workPlace"]').click(function(){
        //bgShow('show');
        $('#chooseMap').animate({
            'display':'block',
            'right':0
        });
        $(this).blur();
        event.preventDefault();
    })
    /**
     * 关闭选择地址
     */
    $('.closeMap').click(function(){
        $('#chooseMap').animate({
            'display':'none',
            'right':'-100%'
        });
    })

    /**
     * 选择需求类型
     */
    $('input[name="workType"]').click(function(){
        //bgShow('show');
        $('#chooseType').animate({
            'display':'block',
            'right':0
        });
        $(this).blur();
        event.preventDefault();
    })
    //调整 选择类型位置
    $('#chooseTypeTitle').css('margin-left',($(window).width()-$('#chooseTypeTitle').width())/2-50);

    /**
     * 关闭选择需求
     */
    $('.closeType').click(function(){
        $('#chooseType').animate({
            'display':'none',
            'right':'-100%'
        });
    })

    //选择帮助类型收缩、展开
    $('.catFirstTitle').click(function(){
        var ul = $(this).next('.catSecUl');
        var i = $('.iconfont',$(this));
        ul.toggle('show');
        if(i.hasClass('icon-menuup')){
            i.removeClass('icon-menuup').addClass('icon-menudown');
        }else {
            i.removeClass('icon-menudown').addClass('icon-menuup');
        }
    })

    $('.cat').click(function(){
        $('input[name="workType"]').val($(this).html());
        $('input[name="workCat"]').val($(this).attr('data-cid'));
        $('.cat').css('color','');
        $('.closeType').click();
        $(this).css('color','#41b07b');
    })

    $('input[name="workTime"]').click(function(){
        $(this).blur();
        event.preventDefault();
    })

})

/**
 * 验证登陆输入
 * @returns {boolean}
 */
function checkLoginInput(){
    var dom = $('#loginBox');
    var phone = $('input[name="phone"]',dom);
    var password = $('input[name="password"]',dom);
    var res = false;
    var msg = '';
    if(!isMobil(phone.val())){
        res = false;
        msg = '手机号格式不对'
    }else if(!checkPwd(password.val())){
        res = false;
        msg = '密码格式不对'
    }else{
        res = true;
    }
    if(res){
        $('#login_sub').css('background','#009966');
    }else{
        $('#login_sub').css('background','#e5e5e5');
    }
    $('#login_tip').html(msg);
    return res;
}

/**
 *提示弹出框
 */
function myAlert(s){
    bgShow('show');
    $('.d-tip').html(s);
    $('#d-wrap').css({
        'display':'block',
        'top':($(window).height()-300)/2
    })
    $('#d-btn-close').on('click',function(){
        $('#d-wrap').css({
            'display':'none',
        });
        bgShow('');
    })
}


/**
 * 显示、隐藏一个遮罩
 */
function bgShow(s){
    var dom = $('#grayBox');
    if(s=='show'){
        dom.addClass('maskShow').css('display','block');
    }else{
        dom.removeClass('maskShow').css('display','');
    }
}

/**
 *写了一个特效
 */
function swingText(dom){
    $('span',$("#"+dom)).each(function(){
        var top = createNum(6);
        $(this).animate({
            'top':top+'px'
        },'slow','swing');
    })
    swingText(dom);
}

function createNum(num){
    return (parseInt(num*Math.random()))-num;
}

function aMap(){
    var lon = 116.397428;
    var lat = 39.90923;
    var marker = null;
    var map = null;
    var container = 'container';
    var geocoder = new AMap.Geocoder({
        radius: 1000,
        extensions: "all"
    });

    this.setLon = function(lon){
        lon = lon;
    }
    this.setLat = function(lat){
        lat = lat;
    }
    this.setContainer = function(con){
        container = con;
    }

    var auto = new AMap.Autocomplete({
        input: "tipinput"
    });

    this.init = function(){
        map = new AMap.Map(container, {
            resizeEnable: true,
            zoom: 13
        });
        addMark();
        //地图中添加地图操作ToolBar插件
        map.plugin(['AMap.ToolBar'], function() {
            //设置地位标记为自定义标记
            var toolBar = new AMap.ToolBar();
            map.addControl(toolBar);
        });
        map.on('mapmove', function(e) {
            log(map.getCenter());
            var p = map.getCenter();
            lat = p.lat;
            lon = p.lng;
            marker.setPosition(p);
        })

        AMap.event.addListener(auto, "select", searchPlace);//注册监听，当选中某条记录时会触发


        //输入文字自动搜索回掉事件
        function searchPlace(e) {
            if (e.poi && e.poi.location) {
                map.setZoom(15);
                map.setCenter(e.poi.location);
                lat = e.poi.location.lat;
                lon = e.poi.location.lng;
                clearMark();
                addMark();
                getPlaceFormLonLat();
            }
        }



    }

    //使用浏览器定位
    this.findPositionByBrowser = function(){
        map.plugin('AMap.Geolocation', function() {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,//是否使用高精度定位，默认:true
                timeout: 10000,          //超过10秒后停止定位，默认：无穷大
                buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                buttonPosition:'RB'
            });
            map.addControl(geolocation);
            geolocation.getCurrentPosition();
            AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
            AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
        });
    }

    this.addMark = function(){
        addMark();
    }

    this.clearMark = function(){
        clearMark();
    }

    this.getMarkPosition = function(){
        return marker.get('position');
    }

    this.getMark = function(){
        return marker;
    }


    function updateMark(){
        var p = marker.getPosition();
        lat = p.lat;
        lon = p.lng;
        getPlaceFormLonLat();
    }

    /**
     * 添加一个标记，只有在不存在标记时才会创建
     */
    function addMark(){
        if(!marker){
            log('创建了一个标记');
            marker = new AMap.Marker({
                icon: "http://webapi.amap.com/theme/v1.3/markers/n/mark_b.png",
                position: [lon, lat],
                draggable: true,
                cursor: 'move',
                raiseOnDrag: true
            });
            marker.setMap(map);
            AMap.event.addListener(marker, "dragend", updateMark);//注册监听，当选中某条记录时会触发
        }
    }


    function clearMark(){
        if(marker){
            log('清除了标记')
            marker.setMap(null);
            marker = null;
        }
    }

    //解析定位结果
    function onComplete(data) {
        log('定位成功');
        lon = data.position.getLng();
        lat = data.position.getLat();
        addMark();
        getPlaceFormLonLat();
    }

    //解析定位错误信息
    function onError(data) {
        log('定位失败');
    }

    function getPlaceFormLonLat(){
        geocoder.getAddress([lon,lat], function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                var address = result.regeocode.formattedAddress; //返回地址描述
                log(address);
                $('input[name="workPlace"]').val(address);
            }
        });
    }

    //获取用户所在城市信息
    function showCityInfo() {
        //实例化城市查询类
        var citysearch = new AMap.CitySearch();
        //自动获取用户IP，返回当前城市
        citysearch.getLocalCity(function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                if (result && result.city && result.bounds) {
                    var cityinfo = result.city;
                    var citybounds = result.bounds;
                    document.getElementById('tip').innerHTML = '您当前所在城市：'+cityinfo;
                    //地图显示当前城市
                    map.setBounds(citybounds);
                }
            } else {
                document.getElementById('tip').innerHTML = result.info;
            }
        });
    }

    function log(str){
        console.log(str);
    }
}


(function($, doc) {
    $.init();
    $.ready(function() {

        //   //级联示例
        var timePicker = new $.PopPicker({
            layer: 3
        });
        timePicker.setData(timeJson);
        var workTimeInput = doc.getElementById('workTimeInput');
        workTimeInput.addEventListener('tap', function(event) {
            timePicker.show(function(items) {
                workTimeInput.value =  (items[0] || {}).text + " " + (items[1] || {}).text + " " + (items[2] || {}).text;
                //返回 false 可以阻止选择框的关闭
                //return false;
            });
        }, false);
    });
})(mui, document);

