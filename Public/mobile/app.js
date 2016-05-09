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



})

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
        });
    })
    swingText(dom);
}

function createNum(num){
    return (parseInt(num*Math.random()))-num;
}