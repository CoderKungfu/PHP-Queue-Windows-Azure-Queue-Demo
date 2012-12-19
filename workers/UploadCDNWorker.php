<?php
class UploadCDNWorker extends PHPQueue\Worker
{
    static private $data_source;
    private $destination_container = 'photoscdn';

    public function __construct()
    {
        parent::__construct();
        $options = array(
              'connection_string' => getenv('wa_blob_connection_string')
            , 'container'         => $this->destination_container
        );
        self::$data_source = \PHPQueue\Base::backendFactory('WindowsAzureBlob', $options);
    }

    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['upload_file']) || !is_file($jobData['upload_file']))
        {
            throw new PHPQueue\Exception\Exception('Result file not found.');
        }
        self::$data_source->putFile($jobData['blobname'], $jobData['downloaded_file']);
        self::$data_source->putFile($jobData['upload_filename'], $jobData['upload_file']);
        $jobData['upload_successful'] = true;
        $this->result_data = $jobData;
    }
}