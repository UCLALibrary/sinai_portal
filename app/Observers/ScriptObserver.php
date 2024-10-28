<?php

namespace App\Observers;

use App\Helpers\JsonUpdaterHelper;
use App\Jobs\UpdateJsonFieldsJob;
use App\Models\Script;


class ScriptObserver
{

    protected $jsonUpdaterHelper;

    public function __construct(JsonUpdaterHelper $jsonUpdaterHelper)
    {
        $this->jsonUpdaterHelper = $jsonUpdaterHelper;
    }

    /**
     * Handle the Location "created" event.
     */
    public function created(Script $script): void
    {
        //
    }

    public function updated(Script $script)
    {
        $fieldsToUpdate = [
            'id' => $script->id,
            'label' => $script->label,
            'writing_system' => $script->writing_system,
        ];

        UpdateJsonFieldsJob::dispatch('script', $script->id, $fieldsToUpdate);
    }

    /**
     * Handle the Script "deleted" event.
     */
    public function deleted(Script $script): void
    {
        //
    }

    /**
     * Handle the Script "restored" event.
     */
    public function restored(Script $script): void
    {
        //
    }

    /**
     * Handle the Script "force deleted" event.
     */
    public function forceDeleted(Script $script): void
    {
        //
    }
}
