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
    "Legendary Grandmaster" => "ff0000.svg",
    "International Grandmaster" => "ff0000.svg",
    "Grandmaster" => "ff0000.svg",
    "International Master" => "ff8c00.svg",
    "Master" => "ff8c00.svg",
    "Candidate Master" => "aa00aa.svg",
    "Expert" => "0000ff.svg",
    "Specialist" => "03a89e.svg",
    "Pupil" => "008000.svg",
    "Newbie" => "808080.svg",
    "Unrated" => "000000.svg"
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

function escapeuserrank($user)
{
    $result = str_replace("_", "__", $user);
    $result = str_replace("-", "--", $result);
    return $result;
}

if (!isset($_GET["user"]) || $_GET["user"] == "") {
    http_response_code(404);
    die("handles: Field should not be empty");
}

$user = trim($_GET["user"]);
set_time_limit(600);
$timeo = 0;
do {   //发现真Unrated会返回OK....所以试下新操作
    $response = getdata($mainUrl . rawurlencode($_GET["user"]));  //获取json数据并转换为数组
    $timeo = $timeo + 1;
} while (
    $response["status"] !== "OK" and
    !preg_match("/handles: User with handle .* not found/", $response["comment"]) and
    $response["comment"] !== "handles: Field should not be empty" and
    $timeo !== 12
);

if ($response["status"] !== "OK") {
    http_response_code(404);
    error_log("Query " . $user . " failed in " . $timeo . " time(s).", 0);
    die($response["comment"]);
}

error_log("Query " . $user . " success in " . $timeo . " time(s).", 0);

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

$handle = $response["result"][0]["handle"];
$rating = $response["result"][0]["rating"];
$rank = $response["result"][0]["rank"] == null ?
    "Unrated" :
    ucwords($response["result"][0]["rank"]);
$color = COLORS[$rank];

$url = $badgeUrl .
    escapeuserrank($handle) . "-" .
    rawurlencode($rank . "  " . $rating . "-") .
    $color .
    $style .
    "&logo=Codeforces" .
    "&link=https://codeforces.com/profile/" . $handle;

error_log("Request url: " . $url, 0);

header("Content-Type: image/svg+xml");
echo file_get_contents($url);
