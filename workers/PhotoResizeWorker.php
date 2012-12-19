<?php
include_once __DIR__ . '/SimpleImage.php';
class PhotoResizeWorker extends PHPQueue\Worker
{
    private $download_folder;
    private $upload_folder;

    public function __construct()
    {
        parent::__construct();
        $this->download_folder = __DIR__ . '/downloads/';
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
        $upload_filename = $this->genThumbName($jobData['blobname']);
        $resized_file_path = $this->upload_folder . $upload_filename;

        $this->resizeImage($jobData['downloaded_file'], $resized_file_path);

        $jobData['upload_filename'] = $upload_filename;
        $jobData['upload_file'] = $resized_file_path;
        $this->result_data = $jobData;
    }

    private function resizeImage($source_file, $resized_file)
    {
        $image = new \SimpleImage();
        $image->load($source_file);
        $w = $image->getWidth();
        $h = $image->getHeight();
        if ($h > $w)
        {
            $image->resizeToHeight(500);
        }
        else
        {
            $image->resizeToWidth(500);
        }
        $image->save($resized_file);
    }

    private function genThumbName($file_path)
    {
        $ext_pos = strrpos($file_path, '.');
        $blob_key = substr($file_path, 0, $ext_pos);
        $ext = substr($file_path, strrpos($file_path, '.'));;
        return sprintf('%s_thumb%s', $blob_key, $ext);
    }
}