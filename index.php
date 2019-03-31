<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 7                                                        |
// +----------------------------------------------------------------------+
// | Authors: abc1763613206 <abc1763613206@163.com>                       |
// +----------------------------------------------------------------------+
//
// $Id:$

$user = $_GET["user"];
$st = $_GET["style"];
$st1 = $_GET["st"];
$badgehd = "Location:https://img.shields.io/badge/";
set_time_limit(600);
$mainUrl = "http://codeforces.com/api/user.info?handles=*";
$timeo = 0;
do {   //发现真Unrated会返回OK....所以试下新操作
  $ratingr = getData(str_replace("*", $user, $mainUrl));  //获取json数据并转换为数组
  $timeo = $timeo +1;
} while ($ratingr["status"] !== "OK" and $ratingr["comment"] !== "handles: User with handle ".$user." not found" and $timeo !== 12);
$styleraw = "?longCache=true&style=*";
if (isset($_GET["style"])) //是否使用自定义style
{
    $style = str_replace("*", $st, $styleraw); //替换
    
} else {
    $style = "?longCache=true&style=for-the-badge";  //默认是for-the-badge
}
if (isset($_GET["st"])) //自定义style缩写，不可重用
{
   switch($st1){
     case "f1" : $style = "?longCache=true&style=flat";break;
     case "f2" : $style = "?longCache=true&style=flat-square";break;
     default: break;
}
    
} 
function getData($url) {
    $headers = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, true);
    return $result;  
}
$ratstr = $ratingr["result"][0]["rating"];   //从数组中提取字符串rating
$rating = intval($ratstr);    //转换为数字
if ($rating >= 3000) {        //开始根据Rating判断段位~
    $name = "-Legendary Grandmaster  ";
    $color = "-red.svg";
} elseif ($rating >= 2600) {
    $name = "-International Grandmaster  ";
    $color = "-red.svg";
} elseif ($rating >= 2400) {
    $name = "-Grandmaster  ";
    $color = "-red.svg";
} elseif ($rating >= 2300) {
    $name = "-International master  ";
    $color = "-ff8c00.svg";
} elseif ($rating >= 2100) {
    $name = "-Master  ";
    $color = "-ff8c00.svg";
} elseif ($rating >= 1900) {
    $name = "-Candidate Master  ";
    $color = "-aa00aa.svg";
} elseif ($rating >= 1600) {
    $name = "-Expert  ";
    $color = "-00f.svg";
} elseif ($rating >= 1400) {
    $name = "-Specialist  ";
    $color = "-03a89e.svg";
} elseif ($rating >= 1200) {
    $name = "-Pupil  ";
    $color = "-008000.svg";
} elseif ($rating !== 0) {   //这里可能翻车，有问题请及时反馈
    $name = "-Newbie  ";
    $color = "-808080.svg";
} else {
    $name = "-Unrated  ";
    $color = "-black.svg";
}
$style = $style. "&logo=Codeforces&link=https://codeforces.com/profile/" . $user; 
$rawc1 = str_replace("_", "__", $user);
$rawc2 = str_replace("-", "--", $rawc1);
$rawr = $badgehd . $rawc2 . $name . $ratstr . $color . $style;
header($rawr); //拼接并输出（修复下划线转义bug）

?>

