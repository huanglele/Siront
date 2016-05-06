<?php
/**
 * Author: huanglele
 * Date: 2016/5/6
 * Time: 下午 01:38
 * Description:
 */

namespace Think\Session\Driver;


class File
{
    protected $lifeTime     = 3600;
    protected $sessionPath = './';
    protected $sessionName  = '';
    protected $handle       = null;

    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName) {
        $this->lifeTime     = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : $this->lifeTime;
        $this->sessionPath = str_replace('\\','/',THINK_PATH."../session/");
        file_put_contents($this->sessionPath.'open','session—open了，'.date('Y-m-d H:i:s'));
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close() {
        $this->gc(ini_get('session.gc_maxlifetime'));
        file_put_contents($this->sessionPath.'close','session—close了，'.date('Y-m-d H:i:s'));
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID) {
        file_put_contents($this->sessionPath.'read','session—read了，'.date('Y-m-d H:i:s'));
        return file_get_contents($this->sessionPath.$sessID);
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData) {
        file_put_contents($this->sessionPath.'write','session—write了，'.date('Y-m-d H:i:s'));
        $res = file_put_contents($this->sessionPath.$sessID,$sessData);
        if($res===false){
            return false;
        }else {
            return true;
        }
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID) {
        file_put_contents($this->sessionPath.'destroy','session—destroy了，'.date('Y-m-d H:i:s'));
        return unlink($this->sessionPath.$sessID);
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime) {
        return true;
    }
}