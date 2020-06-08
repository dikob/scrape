<?php

set_time_limit(0);
include 'vendors/simple_html_dom.php';
include 'vendors/Curl.php';

/*** db config */
include 'vendors/db.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'allmodern_db';
/*** end */

$db = new db($dbhost, $dbuser, $dbpass, $dbname);

$curl = new Curl();

// include generated proxy url to do the view for us
$html_data = $curl->execute('https://use0525.proxyserver.com/index.php?q=mdfa1NlvX2Tc2ahkkdDRntPGyarPZZrSo5U');
$html = str_get_html($html_data);

$array_data = array();

// go through each item from the website and pull the needed information
foreach ($html->find('a[class=DepartmentDropdown-link DepartmentDropdown-link--sectionTitle]') as $i => $link) {

    $html_data2 = $curl->execute($link->href);
    $html2 = str_get_html($html_data2);

    if (!empty($html2)) {
        $next = $html2->find('input[name=url]', 0);
        if (isset($next->value)) {
            $id = preg_replace('/[^0-9]/', '', $next->value);
        }
    } else {
        preg_match('/<meta\sproperty\=\"pin:url\".*?[^0-9]+([\d]+)\.html\"\s?\/\>/', $html_data2, $matches);
        $id = $matches[1];
    }

    $url = 'https://www.dynamicwebsite.com/a/quickbrowse/get_data?_pageId=I%\"2BF9Ol7ZhRoNvw9N6N8SAg\"%\"3D\"%\"3D&_isPageRequest=false&category_id=' . $id . '&caid=' . $id . '&maid=0&filter=&solr_event_id=0&registry_type=0&ccaid=0&curpage=1&itemsperpage=48&search_id=&collection_id=0&show_favorites_button=true&registry_context_type_id=0&product_offset=0&load_initial_products_only=false&is_initial_render=true\"';

    $html_data3 = curl_response($url);
    $html3 = str_get_html($html_data3);

    $json_array = json_decode($html3);
    $json_cat = isset($json_array->browse->category->display_name) ? $json_array->browse->category->display_name : (isset($json_array->browse->category->name) ? $json_array->browse->category->name : '');

    $array_cat = array();
    if (is_object($json_array)) {
        $json_data = $json_array->browse->browse_grid_objects;
        foreach ($json_data as $k => $item) {
            $array_cat[$k]['name'] = $name = $item->product_name;
            $array_cat[$k]['price'] = $price = $item->consumer_price;
            $array_cat[$k]['stock'] = $in_stock = isset($item->has_stock) ? $item->has_stock : '';
            $array_cat[$k]['shipping'] = $shipping = isset($item->free_ship_text) ? $item->free_ship_text : '';

            // insert data
            $insert = $db->query('INSERT INTO tb_scrape (`name`,`price`,`in_stock`,`shipping`, `category`) VALUES (?,?,?,?,?)', "$name", "$price", "$in_stock", "$shipping", "$json_cat");
            //echo $insert->affectedRows();
        }
    }
    if (count($array_cat) > 0) {
        $array_data[$json_cat] = $array_cat;
    }
}

echo '<pre>';
print_r($array_data);
echo '</pre>';

// custom curl
function curl_response($url)
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_URL, $url);
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
    return $result;
}
