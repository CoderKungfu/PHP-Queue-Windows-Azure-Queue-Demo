<?php
class DownloadBlobWorker extends PHPQueue\Worker
{
    private $download_folder;
    static private $data_source;

    public function __construct()
    {
        parent::__construct();
        $options = array(
              'connection_string' => getenv('wa_blob_connection_string')
            , 'container'         => 'photosupload'
        );
        self::$data_source = \PHPQueue\Base::backendFactory('WindowsAzureBlob', $options);
        $this->download_folder = __DIR__ . '/downloads/';
    }

    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['blobname']))
        {
            throw new PHPQueue\Exception\Exception('Blob not found.');
        }
        $blobname = $jobData['blobname'];
        $download_path = $this->download_folder . $blobname;
        self::$data_source->fetchFile($blobname, $download_path);
        $jobData['downloaded_file'] = $download_path;
        $this->result_data = $jobData;
    }
}