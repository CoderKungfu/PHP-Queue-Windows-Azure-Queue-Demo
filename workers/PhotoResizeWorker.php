<?php
include_once __DIR__ . '/SimpleImage.php';
class PhotoResizeWorker extends PHPQueue\Worker
{
    private $upload_folder;
    private $sizes = array(200, 500);

    public function __construct()
    {
        parent::__construct();
        $this->upload_folder = __DIR__ . '/uploads/';
    }

    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['downloaded_file']) || !is_file($jobData['downloaded_file']))
        {
            throw new PHPQueue\Exception\Exception('Download file not found.');
        }

        $uploads = array();
        $uploads[] = array(
                'filename' => $jobData['blobname']
              , 'file'     => $jobData['downloaded_file']
            );

        foreach($this->sizes as $constraint)
        {
            $upload_filename = $this->genThumbName($jobData['blobname'], $constraint);
            $resized_file_path = $this->upload_folder . $upload_filename;
            $this->resizeImage($jobData['downloaded_file'], $resized_file_path, $constraint);
            $uploads[] = array(
                      'filename' => $upload_filename
                    , 'file'     => $resized_file_path
                );
        }
        $jobData['uploads'] = $uploads;
        $this->result_data = $jobData;
    }

    private function resizeImage($source_file, $resized_file, $constraint=300)
    {
        $image = new \SimpleImage();
        $image->load($source_file);
        $w = $image->getWidth();
        $h = $image->getHeight();
        if ($h > $w)
        {
            $image->resizeToHeight($constraint);
        }
        else
        {
            $image->resizeToWidth($constraint);
        }
        $image->save($resized_file);
    }

    private function genThumbName($file_path, $thumb_code='thumb')
    {
        $ext_pos = strrpos($file_path, '.');
        $blob_key = substr($file_path, 0, $ext_pos);
        $ext = substr($file_path, strrpos($file_path, '.'));;
        return sprintf('%s_%s%s', $blob_key, $thumb_code, $ext);
    }
}