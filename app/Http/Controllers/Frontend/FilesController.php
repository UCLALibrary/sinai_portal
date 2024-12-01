<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class FilesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Return a zip file of JSON files for the given record type.
     *
     * @param  String   $recordType
     * @return \Illuminate\Http\Response
     */
    public function downloadZipFile($recordType)
    {
        $this->authorize('download records', User::class);

        // fetch all records from the specified table
        $records = DB::table($recordType)->get();

        // create a temporary file for the zip archive
        $zipFilePath = tempnam(sys_get_temp_dir(), 'zip');
        $zip = new \ZipArchive();
        $zip->open($zipFilePath, \ZipArchive::CREATE);

        // add a json file for each record to the zip archive
        foreach ($records as $record) {
            $zip->addFromString($record->id . '.json', $record->json);
        }

        // close the zip archive
        $zip->close();

        // set the name of the downloaded file
        $downloadFileName = $recordType . '.zip';

        // return the zip file as a download and delete it after sending
        return response()->download($zipFilePath, $downloadFileName)->deleteFileAfterSend(true);
    }
}
