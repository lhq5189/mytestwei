<?php
/*
方倍工作室
CopyRight 2013 All Rights Reserved
*/

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
$wechatObj->responseMsg();
}else{
$wechatObj->valid();
}

class wechatCallbackapiTest
{
public function valid()
{
$echoStr = $_GET["echostr"];
if($this->checkSignature()){
echo $echoStr;
exit;
}
}

private function checkSignature()
{
$signature = $_GET["signature"];
$timestamp = $_GET["timestamp"];
$nonce = $_GET["nonce"];
$token = TOKEN;
$tmpArr = array($token, $timestamp, $nonce);
sort($tmpArr);
$tmpStr = implode($tmpArr);
$tmpStr = sha1($tmpStr);

if($tmpStr == $signature){
return true;
}else{
return false;
}
}

public function responseMsg()
{
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
if (!empty($postStr)){
$this->logger("R ".$postStr);
$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
$RX_TYPE = trim($postObj->MsgType);

switch ($RX_TYPE)
{
case "event":
$result = $this->receiveEvent($postObj);
break;
case "text":
$result = $this->receiveText($postObj);
break;
}
$this->logger("T ".$result);
echo $result;
}else {
echo "";
exit;
}
}

private function receiveEvent($object)
{
$content = "";
switch ($object->Event)
{
case "subscribe":
$content = "欢迎关注方倍工作室 ";
break;
case "unsubscribe":
$content = "取消关注";
break;
}
$result = $this->transmitText($object, $content);
return $result;
}

private function receiveText($object)
{
$keyword = trim($object->Content);

include("weather.php");
$content = getWeatherInfo($keyword);
$result = $this->transmitNews($object, $content);
return $result;
}


private function transmitText($object, $content)
{
$textTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
$result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
return $result;
}

private function transmitNews($object, $arr_item)
{
if(!is_array($arr_item))
return;

$itemTpl = "    <item>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <PicUrl><![CDATA[%s]]></PicUrl>
    <Url><![CDATA[%s]]></Url>
</item>
";
$item_str = "";
foreach ($arr_item as $item)
$item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

$newsTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <Content><![CDATA[]]></Content>
    <ArticleCount>%s</ArticleCount>
    <Articles>
        $item_str</Articles>
</xml>";

$result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
return $result;
}

private function logger($log_content)
{
}
}