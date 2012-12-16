<?php
class SampleWorker extends PHPQueue\Worker
{
    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        $jobData['gotcha'] = "So you are here at " . time();
        $this->result_data = $jobData;
    }
}