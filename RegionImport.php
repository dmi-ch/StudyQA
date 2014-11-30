
<?php
//request params
//$countryId = 1; // Russia
$lang = 0; // russian
$headerOptions = array(
    'http' => array(
        'method' => "GET",
        'header' => "Accept-language: en\r\n" .
            "Cookie: remixlang=$lang\r\n"
    )
);

$methodUrlGetCountry='http://api.vk.com/method/database.getCountries?v=5.27&need_all=1&code=RU&offset=0&count=1';
$streamContext = stream_context_create($headerOptions);
$json = file_get_contents($methodUrlGetCountry, false, $streamContext);

$requestResult=json_decode($json, true);
$countryId=$requestResult['response']['items'][0]['id'];


$methodUrlGetRegions = 'http://api.vk.com/method/database.getRegions?v=5.27&need_all=1&offset=0&count=2&country_id=' . $countryId;
$json = file_get_contents($methodUrlGetRegions, false, $streamContext);
$requestResult = json_decode($json, true);

$regions=$requestResult['response']['items'];

//echo 'CountryId:'.$countryId.' Total regions count: ' . $requestResult['response']['count'] . ' loaded: ' . count($requestResult['response']['items']);

//connection to bd

$mysqli = new mysqli("localhost", "root","root","devstudyqa2", 3306);
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}
$mysqli->set_charset("utf8");
$stmt = $mysqli->prepare("INSERT INTO region (region_id, region_name,region_country_id) VALUES(?,?,?) ");
$affected_rows=0;
$marker = $stmt->param_count;
printf("В запросе %d меток\n", $marker);

foreach ($regions AS $region){

    echo'insert params'.$region['id'].'  '.$region['title'].'    '.$countryId;
    $stmt->bind_param('isi',$region['id'],$region['title'],$countryId);
    $r=$stmt->execute();
    if($r){
       echo 'true';
    }else{
       echo 'false';
    }
    $affected_rows+=$stmt->affected_rows;
}
echo 'Вставлено строк '.$affected_rows;
//echo 'Total regions count: ' . $arr['response']['count'] . ' loaded: ' . count($arr['response']['items']);
