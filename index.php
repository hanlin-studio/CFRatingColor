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
$badgehd = "Location:https://img.shields.io/badge/";
set_time_limit(600);
$mainUrl = "http://codeforces.com/api/user.info?handles=*";
$ratingr = getData(str_replace("*", $user, $mainUrl));  //获取json数据并转换为数组
$styleraw = "?longCache=true&style=*";
if (isset($_GET["style"])) //是否使用自定义style
{
    $style = str_replace("*", $st, $styleraw); //替换
    
} else {
    $style = "?longCache=true&style=for-the-badge";  //默认是for-the-badge
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
if ($rating >= 2900) {        //开始根据Rating判断段位
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
} elseif ($rating >= 2200) {
    $name = "-Master  ";
    $color = "-ff8c00.svg";
} elseif ($rating >= 1900) {
    $name = "-Candidate Master  ";
    $color = "-aa00aa.svg";
} elseif ($rating >= 1600) {
    $name = "-Expert  ";
    $color = "-blue.svg";
} elseif ($rating >= 1400) {
    $name = "-Specialist  ";
    $color = "-03a89e.svg";
} elseif ($rating >= 1200) {
    $name = "-Pupil  ";
    $color = "-008000.svg";
} elseif ($rating > 0) {
    $name = "-Newbie  ";
    $color = "-808080.svg";
} else {
    $name = "-Unrated  ";
    $color = "-black.svg";
}
$rawr = $badgehd . $user . $name . $ratstr . $color . $style;
header(str_replace("_", "__", $rawr)); //拼接并输出（修复下划线转义bug）

?>

