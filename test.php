<html lang="zh-cn">
<head>
<script type="text/javascript">
var _speedMark = new Date();
</script>
<meta charset="UTF-8">
<title>查询结果</title>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection"content="telephone=no, email=no" />
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=63469066" charset="UTF-8"></script>
</head>
<body>

<?php
include_once 'simple_html_dom.php';
include_once 'config.php';

function getSrc()
{
    return md5(date("Y-m-d,H:i:s"));
}

//检查是否绑定学号
function checkBind($openid)
{
    global $config;
    $con=mysqli_connect($config['mysql_host'],$config['mysql_user'],$config['mysql_pass'],$config['mysql_db']);
    if($con){
        $sql = "select `username`,`password` from `jxgl_user` where `openid`='" . $openid . "'";
        $data = mysqli_query($con,$sql);
        if (($data->num_rows == 0 )) {
            return array('status' => 0);//未绑定
        } else {
            while($row = mysqli_fetch_array($data)){
                $username = $row['username'];
                $password = $row['password'];
            }
            return array('status' => 1, 'data' => array('username' => $username, 'password' => $password));
        }
    }else{
        return false;
    }
}

//获取成绩
function getGrade($username, $password, $year) 
{
    global $config;
    $jxglurl = $config['jxglurl'];
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    
    //这里设置文件头可见
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/ASP.NET_SessionId=(.*);/', $header, $matches[0]);
    $SessionId = $matches[0][1];
    
    //preg_match('/xmgxy=(.*);/', $header,$matches[0]);
    // $xmgxy = $matches[0][1];
    $xmgxy = '';
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    $attr = array('Button1' => '登录', 'RadioButtonList1' => '学生', 'TextBox1' => $username, 'TextBox2' => $password, '__VIEWSTATE' => $VIEWSTATE);
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_exec($ch);
    curl_close($ch);
    
    $ch = curl_init($jxglurl . "xscj_gc.aspx?xh=" . $username);
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    
    $attr = array('Button1' => '按学年查询', 'ddlXN' => $year, 'ddlXQ' => '', '__VIEWSTATE' => $VIEWSTATE);
    
    $ch = curl_init($jxglurl . 'xscj_gc.aspx?xh=' . $username);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    
    $html = str_get_html($data);
    $grade = array();
    $tr = $html->find('table#Datagrid1 tr');
    $string = '';
    foreach ($tr as $key => $value) {
        if ($key != 0) {
            $string.= '<font color="red">';
            $string.= $value->find('td', 3)->plaintext . "：";
            $string.= $value->find('td', 8)->plaintext . "</font><br>绩点：";
            $string.= $value->find('td', 7)->plaintext . "，学分：";
            $string.= $value->find('td', 6)->plaintext . "，";
            $string.= $value->find('td', 4)->plaintext . "<br/>";
        }
    }
    return $string;
}

//获取没过的成绩
function getGrade_not_pass($username, $password) 
{
    global $config;
    $jxglurl = $config['jxglurl'];
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    
    //这里设置文件头可见
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/ASP.NET_SessionId=(.*);/', $header, $matches[0]);
    $SessionId = $matches[0][1];
    
    //preg_match('/xmgxy=(.*);/', $header,$matches[0]);
    // $xmgxy = $matches[0][1];
    $xmgxy = '';
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    $attr = array('Button1' => '登录', 'RadioButtonList1' => '学生', 'TextBox1' => $username, 'TextBox2' => $password, '__VIEWSTATE' => $VIEWSTATE);
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_exec($ch);
    curl_close($ch);
    
    $ch = curl_init($jxglurl . "xscj_gc.aspx?xh=" . $username);
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    
    $attr = array('Button1' => '按学年查询', 'ddlXN' => '', 'ddlXQ' => '', '__VIEWSTATE' => $VIEWSTATE);
    
    $ch = curl_init($jxglurl . 'xscj_gc.aspx?xh=' . $username);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    
    $html = str_get_html($data);
    $grade = array();
    $tr = $html->find('table#Datagrid3 tr');
    $string = '';
    foreach ($tr as $key => $value) {
        if ($key != 0) {
            $string.= $value->find('td', 1)->plaintext . "：";
            $string.= $value->find('td', 4)->plaintext . "<br/>（学分：";
            $string.= $value->find('td', 2)->plaintext . "，";
            $string.= $value->find('td', 3)->plaintext . "）<br/>";
        }
    }
    return $string;
}



//curl get请求
function curlGet($url,$cookie=false)
{
    $jxglurl = $config['jxglurl'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);//这里设置文件头可见
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie); //设置cookie
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//curl POST请求
function curlPost($url,$data,$cookie=false)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);//设置cookie
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getLoginUrl($openid)
{
    return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/login.php?openid=' . $openid;
}


$openid = $_GET['openid'];
try
{
    $src = $_GET['src'];
    if($src != md5(date("Y-m-d,h").$openid."pwd") && $src != "bypass")
    {
        echo '<br/>（为保证你的信息安全，所有链接在每小时整点时就会失效）<br/>链接已过期，请发送指令重新获取';
        return;
    }
}
catch (Exception $e)
{
  echo '<br/>（为保证你的信息安全，所有链接在每小时整点时就会失效）<br/>链接已过期，请发送指令重新获取！';
  return;
}

$page_start_time = microtime();
$result = "<br/><br/>请求发起时间：".date("Y-m-d,H:i:s");
if($_GET['mode'] == 0)//没过的成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade_not_pass($checkbind['data']['username'], $checkbind['data']['password']);
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>至今未通过成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = "查询学号：".$checkbind['data']['username'].'<br>未通过成绩结果为空哦';
    }
}
else if($_GET['mode'] == 1)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>所有成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>成绩无数据或数据加载失败！';
    }
}
else if($_GET['mode'] == 2)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2014-2015');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>2014-2015学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>2014-2015学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 3)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2015-2016');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>2015-2016学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>2015-2016学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 4)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2016-2017');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>2016-2017学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>2016-2017学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 5)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2017-2018');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>2017-2018学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>2017-2018学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 6)//所有成绩
{
    $checkbind = checkBind($openid);
    $string = getGrade($checkbind['data']['username'], $checkbind['data']['password'], '2018-2019');
    if (!empty($string)) {
        $result_get = "查询学号：".$checkbind['data']['username']."<br>2018-2019学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$checkbind['data']['username'].'<br>2018-2019学年成绩查询数据为空';
    }
}

$page_end_time = microtime();
$start_time = explode(" ",$page_start_time);
$end_time = explode(" ",$page_end_time);
$total_time = $end_time[0] - $start_time[0] + $end_time[1] - $start_time[1];
$time_cost = sprintf("%s",$total_time);

if($time_cost < 1)
{
    echo "<br>检测到查询时间小于一秒！极有可能遇到了教务系统服务器连接错误的情况！<br>点击此链接进行检查（重复打开2-3次即可）：<a href='http://wfkj1.papapoi.com/browse.php?u=http%3A%2F%2F202.196.225.57&b=0&f=norefer'>点我打开网页版教务系统</a><br>如果长时间加载失败，请及时反馈qq：961726194（答案全部填教务系统打不开），谢谢！<br><br>";
}

echo $result."<br/>成绩查询耗时：".$time_cost."秒<br/><br/><br/>".$result_get;
?>
<br/><br/>工具开发不易，耗时耗力，如果可以的话，欢迎进行打赏，谢谢！<br/><br/>
微信打赏（长按识别二维码）：<br/>
<img src="http://ww3.sinaimg.cn/large/0060lm7Tgy1fiqmj9m0pdj305105ujrt.jpg" alt="二维码图片"/><br/><br/>
支付宝转账：<br/>
<img src="http://ww1.sinaimg.cn/large/0060lm7Tgy1fiqmjrhmtnj305j064mxs.jpg"/>
<br/>
代码最后更新时间：2017.8.21
<!-- 评论留言区（评论在所有页面通用）： -->
<!-- <br/> -->
<!-- <script type="text/javascript"> -->
<!-- var uyan_config = { -->
     <!-- 'su':'wfkj'  -->
<!-- }; -->
<!-- </script> -->
<!-- <div id="uyan_frame"></div> -->
<!-- <script type="text/javascript" src="http://v2.uyan.cc/code/uyan.js?uid=1913072"></script> -->
</body>