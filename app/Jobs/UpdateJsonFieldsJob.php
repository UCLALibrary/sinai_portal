<?php

namespace App\Jobs;

use App\Helpers\JsonUpdaterHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateJsonFieldsJob implements ShouldQueue
{
    use Queueable;

	protected $targetKey;
	protected $targetId;
	protected $fieldsToUpdate;


	public function __construct(string $targetKey, string $targetId, array $fieldsToUpdate)
	{
		$this->targetKey = $targetKey;
		$this->targetId = $targetId;
		$this->fieldsToUpdate = $fieldsToUpdate;
	}

	public function handle(JsonUpdaterHelper $jsonUpdaterHelper)
	{
		$jsonUpdaterHelper->updateJsonFields($this->targetKey, $this->targetId, $this->fieldsToUpdate);
	}
}
