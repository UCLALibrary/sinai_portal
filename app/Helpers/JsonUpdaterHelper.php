<?php

namespace App\Helpers;

class JsonUpdaterHelper
{

	private $modelsToUpdate = [
		'App\Models\Manuscript' => 'json',
		'App\Models\Bibliography' => 'json',
	];

	public function updateJsonFields(string $targetKey, string $targetId, array $fieldsToUpdate)
	{

		foreach ($this->modelsToUpdate as $modelClass => $jsonField) {

			//ToDo: Search for records with jsonb_path_exists() and only retrieve records containing the key `$targetKey`
			$modelInstances = $modelClass::all();

			foreach ($modelInstances as $instance) {

				$jsonData = json_decode($instance->{$jsonField}, true);
				$updatedJson = $this->traverseAndUpdate($jsonData, $targetKey, $targetId, $fieldsToUpdate);

				if ($updatedJson) {
					$instance->{$jsonField} = json_encode($jsonData);
					$instance->save();
				}
			}
		}
	}

	protected function traverseAndUpdate(array &$jsonData, string $targetKey, string $targetId, array $fieldsToUpdate): bool
	{
		$updated = false;

		foreach ($jsonData as $key => &$value) {
			// If the value is an array, continue searching deeper recursively
			if (is_array($value)) {
				// Check if this is the target key array (e.g., 'location' or 'feature')
				if ($key === $targetKey) {
					// If it's an array, loop through and update the matching object inside it
					if (isset($value[0])) {
						// Loop through each item in the targetKey array (handles arrays like location: [{}])
						foreach ($value as &$subValue) {
							// Check if this subValue has the target ID
							if (isset($subValue['id']) && $subValue['id'] === $targetId) {
								// Merge the fields to update with the existing data
								$subValue = array_merge($subValue, $fieldsToUpdate);
								$updated = true; // Mark as updated
							}
						}
					}
					// If the targetKey is a direct object (location: {}), update directly
					elseif (isset($value['id']) && $value['id'] === $targetId) {
						$value = array_merge($value, $fieldsToUpdate);
						$updated = true; // Mark as updated
					}
				}

				// Recursively traverse deeper levels
				$updated = $this->traverseAndUpdate($value, $targetKey, $targetId, $fieldsToUpdate) || $updated;
			}
		}

		return $updated;
	}
}