<?php

namespace App\Helpers;

class JsonDataHelper
{
    public static function attachIdsToModelProperty($model, $propertyName, $ids)
    {
        // decode the json field
        $jsonData = json_decode($model->json, true);

        // ensure the property is an array
        if (!isset($jsonData[$propertyName]) || !is_array($jsonData[$propertyName])) {
            $jsonData[$propertyName] = [];
        }

        // append the ids to the existing property
        $jsonData[$propertyName] = array_merge($jsonData[$propertyName], $ids);

        // ensure the values in property are unique
        $jsonData[$propertyName] = array_unique($jsonData[$propertyName]);

        // encode the json data back to a string
        $model->json = json_encode($jsonData);

        // save the model instance
        $model->save();
    }
}