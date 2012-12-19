<?php
require_once __DIR__ . '/config.php';

$options = array(
      'connection_string' => getenv('wa_blob_connection_string')
    , 'container'         => 'photoscdn'
);
$data_source = \PHPQueue\Base::backendFactory('WindowsAzureBlob', $options);
$photos = $data_source->listFiles();

$sorted_photos = array();
foreach($photos as $photo)
{
    $url = $photo->url;
    if (strpos($url, '_') === false)
    {
        $code_name = substr($url, 0, strpos($url, '.'));
        $sorted_photos[$code_name]['full'] = $url;
    }
    else if (strpos($url, '_200') > 0)
    {
        $code_name = substr($url, 0, strpos($url, '_'));
        $sorted_photos[$code_name]['200'] = $url;
    }
    else if (strpos($url, '_500') > 0)
    {
        $code_name = substr($url, 0, strpos($url, '_'));
        $sorted_photos[$code_name]['500'] = $url;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Uploaded Photos</title>
        <style>
            ul, li{margin:0px;padding:0px;}
            li{float:left;margin:7px;}
        </style>
    </head>
    <body>
        <h2>Uploaded Photos</h2>
        <ul>
            <?php foreach($sorted_photos as $photo):?>
            <li><a href="<?=$photo['500'] ?>"><img src="<?=$photo['200'] ?>" border="0" /></a></li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>