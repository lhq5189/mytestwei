<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class Weixin
{

//回复多图文类型的微信消息
    public function responseNews($postObj ,$arr){
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".count($arr)."</ArticleCount>
					<Articles>";
        foreach($arr as $k=>$v){
            $template .="<item>
						<Title><![CDATA[".$v['title']."]]></Title>
						<Description><![CDATA[".$v['description']."]]></Description>
						<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
						<Url><![CDATA[".$v['url']."]]></Url>
						</item>";
        }

        $template .="</Articles>
					</xml> ";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
    }

    public function responseText($postObj,$content){
        $template = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
//注意模板中的中括号 不能少 也不能多
        $fromUser = $postObj->ToUserName;
        $toUser = $postObj->FromUserName;
        $time = time();
        $msgType = 'text';
        echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
    }

}