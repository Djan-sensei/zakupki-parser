<?php
	function url_get($url) {
    // настройки
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0';
 
    // запрос
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
 
    // ответ
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($content === false) {
        $error = curl_error($ch);
        $content = '';
    } elseif ($code != 200) {
        $error = 'Status: ' . $code;
        $content = '';
    } else {
        $error = false;
    }
 
    curl_close($ch);
    return compact('error', 'content');
}



	$url = 'https://zakupki.gov.ru/epz/eruz/search/results.html?morphology=on&search-filter=%D0%94%D0%B0%D1%82%D0%B5+%D1%80%D0%B0%D0%B7%D0%BC%D0%B5%D1%89%D0%B5%D0%BD%D0%B8%D1%8F&pageNumber='.$_GET['page'].'&sortDirection=false&recordsPerPage=_500&showLotsInfoHidden=false&sortBy=BY_REGISTRY_DATE&participantType_0=on&participantType_1=on&participantType_2=on&participantType_4=on&participantType_5=on&participantType=0%2C1%2C2%2C4%2C5&registered=on&rejectReasonIdNameHidden=%7B%7D&countryRegIdHidden=1268%2C&countryRegIdNameHidden=%7B%221268%22%3A%22%D0%A0%D0%9E%D0%A1%D0%A1%D0%98%D0%AF%22+%7D&address=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0&registryDateFrom=01.01.2021&registryDateTo=30.06.2021';


$resp = url_get($url);
if ($resp['error']) {
    echo "<p>", htmlspecialchars($resp['error']), "</p>";
    exit;
}
	
	
include_once __DIR__ . '/phpQuery.php';

$doc = phpQuery::newDocument($resp['content']);
	
$link = array();
	
$entry = $doc->find('.registry-entry__header-mid__number>a');
foreach ($entry as $row) {
	$ent = pq($row);
	$link[] = array(
		'href' => 'https://zakupki.gov.ru'. $ent->attr('href')
	);
}

$fl1 = fopen("links.txt", 'w') or die("не удалось создать файл");
for($i=0;$i<=count($link);$i++) {
	$txt = $link[$i]['href'].';';
	fwrite($fl1, $txt);
}
fclose($fl1);

echo nl2br(@file_get_contents("links.txt"));
	
?>
