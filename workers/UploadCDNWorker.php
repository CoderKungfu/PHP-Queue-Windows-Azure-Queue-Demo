<?php
class UploadCDNWorker extends PHPQueue\Worker
{
    static private $data_source;
    private $source_container = 'photosupload';
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
        self::$data_source->copy($this->source_container, $jobData['blobname'], $this->destination_container, $jobData['blobname']);
        self::$data_source->putFile($jobData['upload_filename'], $jobData['upload_file']);
        $jobData['upload_successful'] = true;
        $this->result_data = $jobData;
    }
}