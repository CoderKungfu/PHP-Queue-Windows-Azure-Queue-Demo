<?php
require_once dirname(__DIR__) . '/config.php';

$options = array(
      'connection_string' => getenv('wa_blob_connection_string')
    , 'container'         => 'photoscdn'
);
$data_source = \PHPQueue\Base::backendFactory('WindowsAzureBlob', $options);
$photos = $data_source->listFiles();

$sorted_photos = array();
foreach($photos as $photo)
{
    $url = $photo['url'];
    if (strpos($url, '_') === false)
    {
        $code_name = substr($url, 0, strrpos($url, '.'));
        $sorted_photos[$code_name]['full'] = $url;
    }
    else if (strpos($url, '_200') > 0)
    {
        $code_name = substr($url, 0, strrpos($url, '_'));
        $sorted_photos[$code_name]['200'] = $url;
    }
    else if (strpos($url, '_500') > 0)
    {
        $code_name = substr($url, 0, strrpos($url, '_'));
        $sorted_photos[$code_name]['500'] = $url;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Uploaded Photos</title>
        <style>
            ul, li{margin:0px;padding:0px;list-style:none;}
            li{float:left;margin:7px;}
            li a{display:block;width:200px;height:200px;}
            li a:hover{background-color:#efefef;}
            li img{display:block;margin:0px auto 0px auto;}
            #thumbs{width:645px;border:1px solid #333;float:left;}
            #fullimg{width:500px;float:left; padding-left:10px;}
            .clear{clear:both;}
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>
    <body>
        <h2>Uploaded Photos</h2>
        <a href="upload.php" target="_blank">Upload</a>
        <div>
            <div id="thumbs">
                <ul>
                    <?php foreach($sorted_photos as $key => $photo):?>
                    <?php if (isset($photo['500'])): ?>
                    <li><a href="<?=$photo['500'] ?>" data-full-img="<?=$photo['full'] ?>"><img src="<?=$photo['200'] ?>" border="0" /></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div id="fullimg">
                <?php
                $first = array_shift($sorted_photos);
                ?>
                <a href="full.php?img=<?=$first['full']?>" target="_blank"><img src="<?=$first['500']?>" /></a>
            </div>
        </div>
        <script type="text/javascript">
            $('#thumbs a').click(function(evt){
                evt.preventDefault();
                $('#fullimg img').attr('src', $(this).attr('href'));
                $('#fullimg a').attr('href', 'full.php?img=' + $(this).data('fullImg'));
            });
        </script>
    </body>
</html>