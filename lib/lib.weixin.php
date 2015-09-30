<?php
/**
 * WeiXin Lib
 */
define("TOKEN", "Tingo123");
define('APPID', 'wx8e8f87ef14518a40');
define('APPSECRET', 'e2696a5ffbc0e33751632b2748c3a179');

class  WeiXin
{
    private $signature, $timestamp, $nonce, $echostr;

    function __construct($signature, $timestamp, $nonce, $echostr)
    {
        $this->signature = $signature;
        $this->timestamp = $timestamp;
        $this->nonce = $nonce;
        $this->echostr = $echostr;
    }

    /**
     * [valid description]
     * @return [type] [description]
     */
    public function valid()
    {
        if ($this->_checkSignature()) {
            echo $this->echostr;
            exit();
        }
    }

    public function responseMsg()
    {
        $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($post_str)) {
            libxml_disable_entity_loader(true);
            $post_obj = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $rx_type = trim($post_str->MsgType);
            //apache_note('msgtype', $rx_type);
            switch ($rx_type) {
                case 'text':
                    $result = $this->_receiveText($post_obj);
                    break;

                case 'image':
                    $result = $this->_receiveImage($post_obj);
                    break;

                default:
                    $result = $this->_receiveImage($post_obj);
                    break;
            }
            echo  $result;
        }else{
            echo 'empty!';
            exit();
        }
    }



    /*
    THE PRIVATE METHODS
     */

    /**
     * [_checkSignature description]
     * @return [type] [description]
     */
    private function _checkSignature()
    {
        if (!defined("TOKEN")) {
            throw new Exception ("TOKEN IS NOT DEFINED! ");
        }
        $mergArr = array(TOKEN, $this->timestamp, $this->nonce);
        sort($mergArr, SORT_STRING);
        $mergStr = implode($mergArr);
        $mergStr = sha1($mergStr);

        if ($mergStr == $this->signature) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    private function _receiveText($object)
    {
        $func_flag = 0;
        $content_str = 'receive Text: ' . $object->Content;
        $result_str = $this->_transmitText($object, $content_str, $func_flag);
        return $result_str;
    }

    private function _receiveImage($object)
    {
        $func_flag = 0;
        $content_str = "image's url: " . $object->PicUrl;
        $result_str = $this->_transmitText($object, $content_str, $func_flag);
        return $result_str;
    }

    /**
     * [_transmitText description]
     * @param  [type]  $obj     [description]
     * @param  [type]  $content [description]
     * @param  integer $flag [description]
     * @return [type]           [description]
     */
    private function _transmitText($obj, $content, $flag = 0)
    {
        $textTpl = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<MsgId>%d</MsgId>
</xml>
XML;
        return sprintf($textTpl, $obj->FromUserName, $obj->ToUserName, time(), $content, $flag);
    }

    private function _transmitStuff($obj, $type, $content, $flag=0)
    {
        switch ($type) {
            case 'text':
                $inside_content = "<Content><![CDATA[%s]]></Content>";
                break;
            
            case 'image':
                $inside_content = "<PicUrl><![CDATA[%s]]</PicUrl>";
                break;

            case 'voice':
                // $inside_content = "<"
            default:
                # code...
                break;
        }

        $template_xml = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
{$inside_content}
<MsgId>%d</MsgId>
</xml>
XML;
    }

    /**
     * [_recordingLog description]
     * @param  [type] $str  [description]
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private function _recordingLog($str, $path)
    {
        echo $str;
        echo $path;
        $time = time();
        $log_file = date("Y-m-d-H", $time) . ".log";
        if ($fd = fopen($path . "/" . $log_file, "a")) {
            fputs($fd, $str);
            fclose($fd);
        }
    }


}
