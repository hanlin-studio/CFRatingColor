<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 7                                                        |
// +----------------------------------------------------------------------+
// | Authors: abc1763613206 <abc1763613206@163.com>                       |
// |          cubercsl <cubercsl@163.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$

const COLORS = [
    "Legendary Grandmaster" => "ff0000",
    "International Grandmaster" => "ff0000",
    "Grandmaster" => "ff0000",
    "International Master" => "ff8c00",
    "Master" => "ff8c00",
    "Candidate Master" => "aa00aa",
    "Expert" => "0000ff",
    "Specialist" => "03a89e",
    "Pupil" => "008000",
    "Newbie" => "808080",
    "Unrated" => "000000"
];

$badgeUrl = "http://localhost:8080/badge/"; // 使用本地搭建的Shields服务
$mainUrl = "http://codeforces.com/api/user.info?handles=";

function getdata($url)
{
    error_log("Request url: " . $url, 0);
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

function escapehandle($user)
{
    $result = str_replace("_", "__", $user);
    $result = str_replace("-", "--", $result);
    return $result;
}

function getimage($handle, $rank, $color, $rating, $style, $link = false)
{
    global $badgeUrl;
    $url = $badgeUrl .
        escapehandle($handle) . "-" .
        rawurlencode($rank . "  " . $rating . "-") .
        $color . ".svg" .
        $style .
        "&logo=Codeforces" .
        ($link ? "&link=https://codeforces.com/profile/" . $handle : "");
    error_log("Request url: " . $url, 0);
    return file_get_contents($url);
}

header("Content-Type: image/svg+xml");

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    http_response_code(405);
    echo getimage("405", "method not allowed", "critical", null, $style);
    die;
}

if (isset($_GET["style"])) { //是否使用自定义style
    $style = "?longCache=true&style=" . $_GET["style"];
} else {
    $style = "?longCache=true&style=flat";
}
if (isset($_GET["st"])) { //自定义style缩写，不可重用
    switch ($_GET["st"]) {
        case "f1":
            $style = "?longCache=true&style=for-the-badge";
            break;
        case "f2":
            $style = "?longCache=true&style=flat-square";
            break;
        default:
            break;
    }
}

$user = trim($_GET["user"]);
set_time_limit(600);
$timeo = 0;
if (!isset($_GET["user"]) || $_GET["user"] == "") {
    http_response_code(404);
    echo getimage("404", "user not found", "critical", null, $style);
    die;
}
do {   //发现真Unrated会返回OK....所以试下新操作
    $response = getdata($mainUrl . rawurlencode($_GET["user"]));  //获取json数据并转换为数组
    $timeo = $timeo + 1;
} while (
    $response["status"] !== "OK" and
    !preg_match("/handles:.*/", $response["comment"]) and
    $timeo !== 12
);

if ($response["status"] !== "OK") {
    http_response_code(404);
    error_log("Query " . $user . " failed in " . $timeo . " time(s).", 0);
    echo getimage("404", "user not found", "critical", null, $style);
    die;
}

error_log("Query " . $user . " success in " . $timeo . " time(s).", 0);

$handle = $response["result"][0]["handle"];
$rating = $response["result"][0]["rating"];
$rank = $response["result"][0]["rank"] == null ?
    "Unrated" :
    ucwords($response["result"][0]["rank"]);
$color = COLORS[$rank];

echo getimage($handle, $rank, $color, $rating, $style, true);
