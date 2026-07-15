<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://open.spotify.com/episode/7Fjmag81pawL081gbG98SN");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
$html = curl_exec($ch);
curl_close($ch);

$doc = new DOMDocument();
@$doc->loadHTML($html);
$tags = $doc->getElementsByTagName('meta');

$data = [];
foreach ($tags as $tag) {
    $prop = $tag->getAttribute('property');
    $name = $tag->getAttribute('name');
    if ($prop) {
        $data[$prop] = $tag->getAttribute('content');
    }
    if ($name) {
        $data[$name] = $tag->getAttribute('content');
    }
}
print_r($data);
?>
