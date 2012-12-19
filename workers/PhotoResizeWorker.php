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
        $upload_file = $this->upload_folder . $this->genThumbName($jobData['blobname']);

        $image = new \SimpleImage();
        $image->load($jobData['downloaded_file']);
        $w = $image->getWidth();
        $h = $image->getHeight();
        if ($h > $w)
        {
            $image->resizeToHeight(300);
        }
        else
        {
            $image->resizeToWidth(300);
        }
        $image->save($upload_file);

        $jobData['upload_file'] = $upload_file;
        $this->result_data = $jobData;
    }

    private function genThumbName($file_path)
    {
        $ext_pos = strrpos($file_path, '.');
        $blob_key = substr($file_path, 0, $ext_pos);
        $ext = substr($file_path, strrpos($file_path, '.'));;
        return sprintf('%s_thumb%s', $blob_key, $ext);
    }
}