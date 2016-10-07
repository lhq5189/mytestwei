<?php

class Site2Controller extends Controller
{

    public function actionIndex()
    {
        //获得参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];
        $token = TOKEN;
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        } else {
            $this->reponseMsg();
        }
    }

    //接收事件推送与回复
    public function reponseMsg()
    {
        $WeixinModel = new Weixin();
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //$postObj->ToUserName = '';
        //$postObj->FromUserName = '';
        //$postObj->CreateTime = '';
        //$postObj->MsgType = '';
        //$postObj->Event = '';
        // gh_e79a177814ed
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {

//				$content = '欢迎关注我们的微信公众账号' . $postObj->FromUserName . '-' . $postObj->ToUserName;
                $content = '欢迎关注中国工业地产网(www.21dcw.com)!打造21世纪专业的工业地产服务平台!';

                $WeixinModel->responseText($postObj, $content);
            }
            if (strtolower($postObj->Event) == 'click') {
                //如果自定义菜单中的event->click
                if (strtolower($postObj->EventKey) == 'contact') {
                    $content = 'QQ:399179450';
                }

                $WeixinModel->responseText($postObj, $content);
            }
        }

        //厂房搜索
        if(strtolower($postObj->MsgType) == 'text' && strstr(trim($postObj->Content),"+") == true){
            $keywords= explode("+",$postObj->Content);
            $content =$postObj->Content.'00';
            $WeixinModel->responseText($postObj, $content);
        }else{
            $content =$postObj->Content;
            $WeixinModel->responseText($postObj, $content);
        }

        //用户发送tuwen1关键字的时候，回复图文
      /*  if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == 'tuwen2') {

            $arr = array(
                array(
                    'title' => 'imooc',
                    'description' => "imooc is very cool",
                    'picUrl' => 'http://www.imooc.com/static/img/common/logo.png',
                    'url' => 'http://www.imooc.com',
                ),
                array(
                    'title' => 'hao123',
                    'description' => "hao123 is very cool",
                    'picUrl' => 'https://www.baidu.com/img/bdlogo.png',
                    'url' => 'http://www.hao123.com',
                ),
                array(
                    'title' => 'qq',
                    'description' => "qq is very cool",
                    'picUrl' => 'http://www.imooc.com/static/img/common/logo.png',
                    'url' => 'http://www.qq.com',
                ),
            );

            $WeixinModel->responseNews($postObj, $arr);


            //注意：进行多图文发送时，图文个数不能超过10个
        } else {
            switch (trim($postObj->Content)) {
                case 1:
                    $content = '您输入的数字是1';
                    break;
                case 2:
                    $content = '您输入的数字是2';
                    break;
                case 3:
                    $content = '您输入的数字是3';
                    break;
                case 4:
                    $content = "<a href='http://www.imooc.com'>慕课</a>";
                    break;
                case '英文':
                    $content = 'imooc is ok';
                    break;
                default:
//                    $content = "对不起，你的输入有误，请重新输入!";

                    //读取数据库内容
                    $keywords =$postObj->Content;
                    if(!is_numeric($keywords)){
                       $arr   = News::model()->filter2('id,title,thumb',3,"thumb!=''")->findAll();
                        $WeixinModel->responseTestNews($postObj,$arr);
                    }

                    //天气预报
//                    $ch = curl_init();
//                    $url = 'http://apis.baidu.com/apistore/weatherservice/weather?cityname=' . $postObj->Content;
//                    $header = array(
//                        'apikey:e73f5fe0a98789b96e260d8c7de50b35',
//                    );
//                    // 添加apikey到header
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                    // 执行HTTP请求
//                    curl_setopt($ch, CURLOPT_URL, $url);
//                    $res = curl_exec($ch);
//
//                    $arr = json_decode($res, true);
//                    $content = $arr['retData']['city'] . $arr['retData']['date'] . ":" . $arr['retData']['weather'] . "\n" . "最高温度:" . $arr['retData']['h_tmp'] . "\n最低温度:" . $arr['retData']['l_tmp'];
//
            }


            $WeixinModel->responseText($postObj, $content);

        }*/

    }

    //获取accessToken
    /*    function GetAccessToken(){
            //请求URL
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $data   =curl_exec($ch);

            if(curl_errno($ch)){
                var_dump(curl_errno($ch));
            }
            curl_close($ch);
            $arr    =json_decode($data,true);

        }*/

    //获取服务器ip地址
    public function actionGetWxServerIp()
    {
        $ACCESS_TOKEN = "V4QL26eYib1hEo1Dm2xdnm-hWX9rTpfYKBejFZH_bldsi4-6zuLngFHlNjSPJ-YYincYZCITSDL-bdVNih32hajpKqiLSOwZSv-P7mHzYehzYR2sihln5WcTptbxtis-ZQXbAGANEL";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=" . $ACCESS_TOKEN;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump(curl_errno($ch));
        }
        curl_close($ch);
        $arr = json_decode($data, true);
        var_dump($arr);
    }

    function http_curl($url, $type = 'get', $res = "json", $arr = '')
    {
        //初始化curl;
        $ch = curl_init();
//        设置url参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
//        采集
        $output = curl_exec($ch);

        if ($res = 'json') {
            if (curl_errno($ch)) {
//                请求失败
                return curl_errno($ch);
            } else {
//                请求成功
                return json_decode($output, true);
            }

        }
        curl_close($ch);
    }

    //返回session access_token解决方法
    public function actionGetAccessToken()
    {
        if (Yii::app()->session['access_token'] && Yii::app()->session['expire_time'] > time()) {
            $access_token = Yii::app()->session['access_token'];
            return $access_token;
        } else {
            //如果access_token不存在或者已经过期，重新取access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APPID . "&secret=" . APPSECRET;
            $res = $this->http_curl($url, 'get', 'json');
            $access_token = $res['access_token'];
            //将重新获取到的access_token存到session
            Yii::app()->session['access_token'] = $access_token;
            Yii::app()->session['expire_time'] = time() + 7000;
            return $access_token;
        }
    }

    //自定义菜单
    public function actionDefinedItem()
    {
        header('content-type:text/html;charset=utf-8');
        $access_token = $this->actionGetAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        $postArr = array(
            'button' => array(
                array(
                    'type' => 'view',
                    'name' => urlencode('找厂房'),
                    'url' => 'http://www.21dcw.com/changfang_chuzu.html',
                ),        //第一个一级菜单
                array(
                    'type' => 'view',
                    'name' => urlencode('找仓库'),
                    'url' => 'http://www.21dcw.com/cangkug_chuzu.html',
                ),
                array(
                    'name' => urlencode('更多'),
                    'sub_button' => array(
                        array(
                            'type' => 'view',
                            'name' => urlencode('找土地'),
                            'url' => 'http://www.21dcw.com/tudi_chuzu.html',
                        ),
                        array(
                            'type' => 'click',
                            'name' => urlencode('联系QQ'),
                            'key' => 'contact',
                        ),
                    ),

                ),
            )
        );

        $postJson = urldecode(json_encode($postArr));
        $res = $this->http_curl($url, 'post', 'json', $postJson);
        var_dump($res);
    }

    public function actionTest()
    {
        //获取百度
        ////初始化
        $ch = curl_init();
        $url = "http://www.baidu.com";
        //设置url参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //采集
        $data = curl_exec($ch);
        curl_close($ch);
        var_dump($data);
    }


}