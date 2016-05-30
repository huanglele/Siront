/**
 * Created by huanglele on 2016/5/9.
 */

$(window).ready(function(){
    //个人头像点击事件
    $('#btnMenu').click(function(){
        log('发送点击');
        if(isLogin()){
            $('#menu').addClass('hidden').removeClass('visible');
            $('.closeMenu').addClass('visible');
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
        $('#menu').removeClass('hidden').addClass('visible');
        $('.closeMenu').removeClass('visible');
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
                        setV('uid',data.uid);
                        $api.rmStorage('type');
                        getUserInfo();
                        $('#closeLogin').click();
                    }
                }
            })
        }
    })


    /**
     * 选择工作地点
     */
    $('input[name="workPlace"]').click(function(){
        //bgShow('show');
        chooseMap();
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

    //提交任务
    $('#sub_task').click(function(){
        if(isLogin()){
            var data = checkTaskInput();
            if(data){
                $.ajax({
                    'url':baseUrl+'/Ajax/addTask',
                    'type':"POST",
                    'dataType':'json',
                    'data':data,
                    beforeSend:function(){
                        bgShow('show');
                    },
                    success:function(res){
                        if(res.status=='success'){
                            myAlert('发布成功');
                            $('input').val('');
                            $('textarea').val('');
                            ajaxSend("<{:U('user/postTaskAgain')}>?tid="+res.taskId);
                        }else if(res.status=='error'){
                            myAlert(res.msg);
                        }else {
                            myAlert('未知错误');
                        }
                    },
                    complete: function(){
                        bgShow();
                    }
                })
            }
        }else {
            $('#btnMenu').click();
        }
    })

})

/**
 * 选择地图
 */
function chooseMap(){
    //$('#chooseMap').animate({
    //    'display':'block',
    //    'right':0
    //});
    event.preventDefault();
    $(this).blur();
    api.openFrame({
        name : 'chooseMap',
        url : 'widget://html/chooseMap.html',
        bounces : false,
        rect : {
            w : 'auto',
            h : 'auto'
        },
        progress : {
            type : 'page'
        },
        animation:{
            type:"push",                //动画类型（详见动画类型常量）
            subType:"from_right",       //动画子类型（详见动画子类型常量）
            duration:300                //动画过渡时间，
        }
    })
}


/**
 * 判断是否登录了
 */
function isLogin(){
    var uInfo = getV('uInfo');
    if(uInfo.status=='success'){
        return true;
    }else {
        return false;
    }
}

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
        $('#login_sub').css('background','#3399ff');
    }else{
        $('#login_sub').css('background','#e5e5e5');
    }
    $('#login_tip').html(msg);
    return res;
}

/**
 * 验证发布任务的数据
 * @returns boolean|json  失败返回false，成功返回需要发送的数据
 */
function checkTaskInput(){
    var place = $('input[name="workPlace"]').val();
    var cityCode =  $('input[name="cityCode"]').val();
    var lon =  $('input[name="lon"]').val();
    var lat =  $('input[name="lat"]').val();
    if(cityCode==''){
        myAlert('请设置位置');return false;
    }
    var type =  $('input[name="workCat"]').val();
    if(type==''){
        myAlert('请设置求助类型');return false;
    }
    var time = $('input[name="time"]').val();
    if(time==''){
        myAlert('请设置时间');return false;
    }
    var title = $('#workTitleInput').val();
    var tel = $('input[name="tel"]').val();
    if(!isMobil(tel)){
        myAlert('填写正确的联系方式');return false;
    }
    var desc = $('#workDescInput').val();
    if(desc==''){
        myAlert('请描述一下问题');return false;
    }

    var data = {
        'place':place,
        'lon' : lon,
        'lat':lat,
        'cityCode':cityCode,
        'type':type,
        'time':time,
        'title':title,
        'tel':tel,
        'desc':desc
    }
    return data;
}

/**
 *提示弹出框
 */
function myAlert(s){
    bgShow('show');
    $('.d-tip').html(s);
    $('#d-wrap').css({
        'display':'block',
        'top':($(window).height()-300)/2,
        'left':($(window).width()-280)/2
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

/**
 * 验证是不是电话号码包括手机号和座机。
 * @param s
 */
function isTel(s){
    var patrn=/^0\d{2,3}-?\d{7,8}$/;
    if (patrn.exec(s) || isMobil(s)) return true;
    return false;
}

/**
 * 验证是不是手机号
 * @param s
 * @returns {boolean}
 */
function isMobil(s) {
    var patrn=/^(13[0-9]|14[5|7]|15[0-9]|17[0-9])\d{8}$/;
    if (!patrn.exec(s)) return false;
    return true;
}

/**
 *去除字符串两端空格
 * @param s
 * @returns {string|void|XML|*|{by}}
 */
function trim(s){
    return String(s).replace(/(^\s*)|(\s*$)/g, "");
}

/**
 * 发送ajax操作，
 * @param url 请求地址
 */
function ajaxSend(url){
    $.ajax({
        'url':url
    })
}


(function($, doc) {
    $.init();
    $.ready(function() {

        //级联示例
        var timePicker = new $.PopPicker({
            layer: 3
        });
        timePicker.setData(timeJson);
        var workTimeInput = doc.getElementById('workTimeInput');
        workTimeInput.addEventListener('tap', function(event) {
            timePicker.show(function(items) {
                workTimeInput.value =  (items[0] || {}).text + " " + (items[1] || {}).text + "点 " + (items[2] || {}).text+"分";
                doc.getElementById('time').value = trim(items[0].value) + " " + trim(items[1].value)+ ":" + trim(items[2].value);
                //返回 false 可以阻止选择框的关闭
                //return false;
            });
        }, false);
    });
})(mui, document);

