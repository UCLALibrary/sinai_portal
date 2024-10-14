<?php

namespace App\Observers;

use App\Helpers\JsonUpdaterHelper;
use App\Jobs\UpdateJsonFieldsJob;
use App\Models\Location;
use App\Models\Manuscript;

class LocationObserver
{

	protected $jsonUpdaterHelper;

	public function __construct(JsonUpdaterHelper $jsonUpdaterHelper)
	{
		$this->jsonUpdaterHelper = $jsonUpdaterHelper;
	}

    /**
     * Handle the Location "created" event.
     */
    public function created(Location $location): void
    {
        //
    }

	public function updated(Location $location)
	{
		$fieldsToUpdate = [
			'repository' => $location->repository,
			'collection' => $location->collection,
		];

		UpdateJsonFieldsJob::dispatch('location', $location->id, $fieldsToUpdate);
	}

    /**
     * Handle the Location "deleted" event.
     */
    public function deleted(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "restored" event.
     */
    public function restored(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "force deleted" event.
     */
    public function forceDeleted(Location $location): void
    {
        //
    }
}
