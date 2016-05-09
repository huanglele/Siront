/**
 * Created by huanglele on 2016/5/9.
 */

/**
 * 判断是否是手机号
 * @param s
 * @returns {boolean}
 */
function isMobil(s) {
    var patrn=/^(13[0-9]|14[5|7]|15[0-9]|17[0-9])\d{8}$/;
    if (!patrn.exec(s)) return false
    return true
}

/**
 * 判断是否是电话号码
 * @param s
 * @returns {boolean}
 */
function isTelCode(s){
    var patrn=/^([0-9])\d{4,6}$/;
    if (!patrn.exec(s)) return false
    return true
}

/**
 * 检测密码格式
 * @param s
 * @returns {boolean}
 */
function checkPwd(s){
    var patrn=/^([^\s]){6,16}$/;
    if (!patrn.exec(s)) return false
    return true
}

function log(s){
    console.log(s);
}