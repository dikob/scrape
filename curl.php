<?php

// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://www.allmodern.com/a/quickbrowse/get_data?_pageId=I%\"2BF9Ol7ZhRoNvw9N6N8SAg\"%\"3D\"%\"3D&_isPageRequest=false&category_id=1866680&caid=1866680&maid=0&filter=&solr_event_id=0&registry_type=0&ccaid=0&curpage=1&itemsperpage=48&search_id=&collection_id=0&show_favorites_button=true&registry_context_type_id=0&product_offset=0&load_initial_products_only=false&is_initial_render=true\"');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0';
$headers[] = 'Accept: application/json';
$headers[] = 'Accept-Language: en-US,en;q=0.5';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'X-Parent-Txid: I+F9Ol7ZhRoNvw9N6N8SAg==';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Cookie: CSNUtId=23e9f4e3-5ed8-affb-6aab-0f2d93f66902; vid=23e9f4e3-5ed8-affb-6aab-0f2d93f66902; canary=0; CSNPersist=page_of_visit%\"3D262\"%\"26latestRefid\"%\"3DDTR1;';
$headers[] = 'Te: Trailers';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


echo $result;
