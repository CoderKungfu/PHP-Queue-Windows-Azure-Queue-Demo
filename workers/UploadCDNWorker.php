<?php
class UploadCDNWorker extends PHPQueue\Worker
{
    static private $data_source;

    public function __construct()
    {
        parent::__construct();
        $options = array(
              'connection_string' => getenv('wa_blob_connection_string')
            , 'container'         => 'photoscdn'
        );
        self::$data_source = \PHPQueue\Base::backendFactory('WindowsAzureServiceBlob', $options);
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
        self::$data_source->put($jobData['blobname'], $jobData['upload_file']);
        $jobData['upload_successful'] = true;
        $this->result_data = $jobData;
    }
}