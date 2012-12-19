<?php
class PhotosQueue extends PHPQueue\JobQueue
{
    private $dataSource;
    private $blobSource;
    private $queueWorker = array('DownloadBlob', 'PhotoResize', 'UploadCDN');
    private $resultLog;

    public function __construct()
    {
		parent::__construct();
		$queue_options = array(
              'connection_string' => getenv('queue_connection_string')
            , 'queue'   => 'photosqueue'
        );
        $this->dataSource = \PHPQueue\Base::backendFactory('WindowsAzureServiceBus', $queue_options);

        $options = array(
              'connection_string' => getenv('wa_blob_connection_string')
            , 'container'         => 'photosupload'
        );
        $this->blobSource = \PHPQueue\Base::backendFactory('WindowsAzureBlob', $options);

        $this->resultLog = \PHPQueue\Logger::createLogger(
                              'NoobLogger'
                            , PHPQueue\Logger::INFO
                            , __DIR__ . '/logs/photos.log'
                        );
    }

    public function addJob(array $newJob)
    {
        if (empty($newJob['file']) || !is_file($newJob['file']))
        {
            throw new \PHPQueue\Exception\Exception('File not found.');
        }
        if (empty($newJob['filename']))
        {
            $newJob['filename'] = $newJob['file'];
        }
        $newJob['blobname'] = $this->genBlobName($newJob['filename']);
        $this->blobSource->putFile($newJob['blobname'], $newJob['file']);
        unset($newJob['file']);

        $formatted_data = array('worker'=>$this->queueWorker, 'data'=>$newJob);
        $this->dataSource->add($formatted_data);
		$this->resultLog->addInfo('Adding new job: ', $newJob);
        return true;
    }

    public function getJob()
    {
        $data = $this->dataSource->get();
        $nextJob = new \PHPQueue\Job($data, $this->dataSource->last_job_id);
        $this->last_job_id = $this->dataSource->last_job_id;
        return $nextJob;
    }

    public function updateJob($jobId = null, $resultData = null)
    {
        $this->blobSource->clear($resultData['blobname']);
        $this->resultLog->addInfo('Result: ID='.$jobId, $resultData);
    }

    public function clearJob($jobId = null)
    {
        $this->dataSource->clear($jobId);
    }

    public function releaseJob($jobId = null)
    {
        $this->dataSource->release($jobId);
    }

    private function genBlobName($file_path)
    {
        $blob_key = md5( sprintf('%s-%s', $file_path, time()) );
        $ext = substr($file_path, strrpos($file_path, '.'));
        return sprintf('%s%s', $blob_key, $ext);
    }
}