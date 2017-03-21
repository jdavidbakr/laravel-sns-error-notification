<?php 

namespace jdavidbakr\LaravelSNSErrorNotification\Mocks;

class AwsMock {

	protected $lastPublish = [];

	public function createClient()
	{
		return $this;
	}

	public function publish($data)
	{
		$this->lastPublish = $data;
	}
}