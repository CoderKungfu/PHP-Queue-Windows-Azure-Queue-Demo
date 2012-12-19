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
        if (empty($jobData['uploads']))
        {
            throw new PHPQueue\Exception\Exception('Result files not found.');
        }
        $status = true;
        foreach($jobData['uploads'] as $upload)
        {
            if (is_file($upload['file']))
            {
                self::$data_source->putFile($upload['filename'], $upload['file']);
            }
            else
            {
                $jobData['errors'][] = sprintf('Unable to upload %s', $upload['file']);
                $status = false;
            }
        }
        $jobData['success'] = $status;
        $this->result_data = $jobData;
    }
}