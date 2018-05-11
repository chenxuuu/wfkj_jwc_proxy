<?php

//error_reporting(0);
include_once ('function.php');
include_once ('config.php');

/**
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author NetPuter <netputer@gmail.com>
 */

require ('Wechat.php');

/**
 * 微信公众平台演示类
 */
class MyWechat extends Wechat
{
    
    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
        $this->responseText("hi！欢迎关注本微信公众号！\n查询成绩请发送“查成绩”\n查询其他指令请发送“帮助”\n本公众号由晨旭运营\n基于万方内网代理系统编写\n反馈/广告请加qq961726194");
    }
    
    /**
     * 用户已关注时,扫描带参数二维码时触发，回复二维码的EventKey (测试帐号似乎不能触发)
     *
     * @return void
     */
    protected function onScan() {
        
        //$this->responseText('二维码的EventKey：' . $this->getRequest('EventKey'));
        
    }
    
    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
        
        // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
        
    }
    
    /**
     * 上报地理位置时触发,回复收到的地理位置
     *
     * @return void
     */
    protected function onEventLocation() {
        
        //$this->responseText('收到了位置推送：' . $this->getRequest('Latitude') . ',' . $this->getRequest('Longitude'));
        
    }
    
    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
        $content = $this->getRequest('content');
        $openid = $this->getRequest('FromUserName');
        $time = $this->getRequest('CreateTime');
        
        /*
        if (mb_substr($content, 0, 3, 'utf-8') == '图书馆') {
            $string = library($content);
            $this->responseText($string);
            return;
        }
        */
        
        if ($content == '查询未通过成绩' || $content == '未通过') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('未通过成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=0&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade_not_pass($checkbind['data']['username'], $checkbind['data']['password']);
                if (!empty($string)) {
                    $this->responseText("至今未通过成绩如下：\n".$string);
                } else {
                    $this->responseText('未通过成绩结果为空哦');
                }
                return;
            }
        }
        
        if ($content == '成绩' || $content == '查成绩') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('所有成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=1&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '');
                if (!empty($string)) {
                    $this->responseText("所有成绩如下：\n".$string);
                } else {
                    $this->responseText('当前请求无数据或数据加载失败！');
                }
                return;
            }
        }
        
        if ($content == '成绩14' || $content == '14') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('2014-2015学年成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=2&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2014-2015');
                if (!empty($string)) {
                    $this->responseText("2014-2015学年成绩如下：\n".$string);
                } else {
                    $this->responseText('2014-2015学年成绩查询数据为空');
                }
                return;
            }
        }
        if ($content == '成绩15' || $content == '15') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('2015-2016学年成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=3&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2015-2016');
                if (!empty($string)) {
                    $this->responseText("2015-2016学年成绩如下：\n".$string);
                } else {
                    $this->responseText('2015-2016学年成绩查询数据为空');
                }
                return;
            }
        }
        if ($content == '成绩16' || $content == '16') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('2016-2017学年成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=4&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2016-2017');
                if (!empty($string)) {
                    $this->responseText("2016-2017学年成绩如下：\n".$string);
                } else {
                    $this->responseText('2016-2017学年成绩查询数据为空');
                }
                return;
            }
        }
        if ($content == '成绩17' || $content == '17') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('2017-2018学年成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=5&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2017-2018');
                if (!empty($string)) {
                    $this->responseText("2017-2018学年成绩如下：\n".$string);
                } else {
                    $this->responseText('2017-2018学年成绩查询数据为空');
                }
                return;
            }
        }
        if ($content == '成绩18' || $content == '18') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $this->responseText('2018-2019学年成绩点击查看：'."\n".'http://wfkj1.papapoi.com/test.php?mode=6&openid='.$openid.'&src='.md5(date("Y-m-d,h").$openid."pwd"));return;
                $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2018-2019');
                if (!empty($string)) {
                    $this->responseText("2018-2019学年成绩如下：\n".$string);
                } else {
                    $this->responseText('2018-2019学年成绩查询数据为空');
                }
                return;
            }
        }
        
        
        if ($content == '考试安排') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $string = getExam($checkbind['data']['username'], $checkbind['data']['password']);
                if (!empty($string)) {
                    $this->responseText($string);
                } else {
                    $this->responseText('考试安排请求无数据');
                }
                return;
            }
        }
        
        // if ($content == '更换学号') {
            // $data = unBind($openid);
            // /*
            // if ($data['status'] == 0) {
                // $this->responseText('您还未绑定！');
            // } else if ($data['status'] == 1) {
                // $this->responseText('解绑成功！');
            // }
            // */
            // $url = getLoginUrl($openid);
            // $this->responseText('<a href=' . "\"" . $url . "\"" . '>点击绑定新学号</a>'."\n".'http'.$url);
        // }
        
        if ($content == '帮助') {
            $this->responseText("查询成绩请发送“查成绩”；\n查询挂了/补考也没过的成绩请发送“未通过”；\n按学年查询成绩请发送“成绩14”即可查询2014-2015学年，以此类推；\n查询考试安排请发送“考试安排”；\n查询今天课程请发送“课表”；\n选课请上网页版进行操作；\n本公众号由晨旭运营；\n基于万方内网代理系统编写；\n反馈/广告/更换绑定的账号请加qq961726194");
            return;
        }
        
        if (mb_substr($content,0,2,'utf-8') == '课表') {
            $checkbind = checkBind($openid);
            if ($checkbind['status'] == 0) {
                $url = getLoginUrl($openid);
                $this->responseText('请先绑定学号' . "\n" . '<a href=' . "\"" . $url . "\"" . '>点击绑定学号</a>'."\n".'http'.$url);
                return;
            } else if ($checkbind['status'] == 1) {
                $week = mb_substr($content,2,1,'utf-8');
                if($week==''){
                	$week = date('w',$time);
                }
                $string = getClass($checkbind['data']['username'],$checkbind['data']['password'],$week);
            }
            $this->responseText($string);
            return;
        }

        if ($content == '反馈') {
            $this->responseText('反馈/广告请加qq961726194');
            return;
        }
        
        if ($content == '测试') {
            $this->responseText('ok');
            return;
        }
        
        if ($content == '选课') {
            $this->responseText('选课请上wfkj.papapoi.com网页版进行操作');
            return;
        }
        
        $this->responseText('命令不存在，发送“帮助”可获取指令说明');
        return;
    }
    
    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    protected function onImage() {
        $items = array(new NewsResponseItem('标题一', '描述一', $this->getRequest('picurl'), $this->getRequest('picurl')), new NewsResponseItem('标题二', '描述二', $this->getRequest('picurl'), $this->getRequest('picurl')),);
        
        //$this->responseNews($items);
        
    }
    
    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    protected function onLocation() {
        
        //$num = 1 / 0;
        // 故意触发错误，用于演示调试功能
        
        //$this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
        
    }
    
    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
        
        //$this->responseText('收到了链接：' . $this->getRequest('url'));
        
    }
    
    /**
     * 收到语音消息时触发，回复语音识别结果(需要开通语音识别功能)
     *
     * @return void
     */
    protected function onVoice() {
        $this->responseText('收到了语音消息,识别结果为：' . $this->getRequest('Recognition'));
    }
    
    /**
     * 收到自定义菜单消息时触发，回复菜单的EventKey
     *
     * @return void
     */
    protected function onClick() {
        
        //$this->responseText('你点击了菜单：' . $this->getRequest('EventKey'));
        
    }
    
    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown() {
        
        //$this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
        
    }
}
global $config;
$wechat = new MyWechat($config['weixin_token']);
$wechat->run();
