<?php
require_once dirname(__DIR__) . '/config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Image Upload</title>
    </head>
    <body>
        <?php
        if( isset($_POST['submit']) ):
            $payload = array(
                  'filename' => $_FILES['uploaded_image']['name']
                , 'file' => $_FILES['uploaded_image']['tmp_name']
            );
            try
            {
                $queue = \PHPQueue\Base::getQueue('Photos');
                \PHPQueue\Base::addJob($queue, $payload);
                $status = 'Image Uploaded.';
            }
            catch (\Exception $ex)
            {
                $status = $ex->getMessage();
            }
        ?>
            <h2><?=$status?></h2>
            <p><a href="upload.php">Upload Another</a></p>
        <?php else: ?>
        <h2>PHP-Queue Photo Upload Demo</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="uploaded_image" />
            <input type="submit" name="submit" value="Upload File" />
         </form>
        <?php endif; ?>
    </body>
</html>