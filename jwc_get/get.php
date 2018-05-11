<?php
include_once 'simple_html_dom.php';
include_once 'config.php';


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


$id = $_GET['id'];
$psw = base64_decode($_GET['psw']);
if($_GET['mode'] == 0)//没过的成绩
{
    $string = getGrade_not_pass($id, $psw);
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>至今未通过成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = "查询学号：".$id.'<br>未通过成绩结果为空哦';
    }
}
else if($_GET['mode'] == 1)//所有成绩
{
    $string = getGrade($id, $psw, '');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>所有成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>成绩无数据或数据加载失败！';
    }
}
else if($_GET['mode'] == 2)//所有成绩
{
    $string = getGrade($id, $psw, '2014-2015');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>2014-2015学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>2014-2015学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 3)//所有成绩
{
    $string = getGrade($id, $psw, '2015-2016');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>2015-2016学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>2015-2016学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 4)//所有成绩
{
    $string = getGrade($id, $psw, '2016-2017');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>2016-2017学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>2016-2017学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 5)//所有成绩
{
    $string = getGrade($id, $psw, '2017-2018');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>2017-2018学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>2017-2018学年成绩查询数据为空';
    }
}
else if($_GET['mode'] == 6)//所有成绩
{
    $string = getGrade($id, $psw, '2018-2019');
    if (!empty($string)) {
        $result_get = "查询学号：".$id."<br>2018-2019学年成绩如下：<br/>".$string;
    } 
    else 
    {
         $result_get = '查询学号：'.$id.'<br>2018-2019学年成绩查询数据为空';
    }
}

echo $result_get;
?>