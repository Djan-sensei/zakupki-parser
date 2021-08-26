<script src="/js/jquery-3.3.1.min.js"></script>

<?php

echo '<a id="next" href="/parser/?page='.($_GET['page']+1).'">ДАЛЕЕ</a><br><br>';

include_once __DIR__ . '/phpQuery.php';

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

$links = @file_get_contents("links.txt");
$pikf = explode(';', $links);
		
$url = $pikf[$_GET['page']];
 
$resp = url_get($url);
if ($resp['error']) {
    echo "<p>", htmlspecialchars($resp['error']), "</p>";
    exit;
}
 
 
$data = fopen("data.txt", 'a+') or die("не удалось создать файл");
$txt = '';
 
$doc2 = phpQuery::newDocument($resp['content']);
$entry2 = $doc2->find('.blockInfo>.col>.blockInfo__section');

$entry3 = $doc2->find('.tableBlock__body>tr>td');

$t=0;
foreach ($entry3 as $row) {
	if($t == 0) {
		$fl = pq($row);
		$txt .= $fl->text() .';';
	}
	$t++;
}

foreach ($entry2 as $row) {
	
	$fl = pq($row);
	$name = $fl->find('.section__title')->text();
	$value = $fl->find('.section__info')->text();
	
	if($name == 'ИНН') {
		$txt .= $value.';';
	}
	if($name == 'Сокращенное наименование') {
		$txt .= $value.';';
	}
	if($name == 'Адрес электронной почты') {
		$txt .= $value.';';
	}
	if($name == 'Контактный телефон') {
		$txt .= $value.';
';
	}
	
}

echo $txt;

fwrite($data, $txt);
fclose($data);
			
// echo '<hr>';
// echo nl2br(@file_get_contents("data.txt"));

?>

<?php if(!empty($txt)) { ?>
	<script>
		setTimeout(function(){
				window.location.href="/parser/?page=<?php echo ($_GET['page']+1); ?>";
		}, 2000);
	</script>
<?php } ?>
