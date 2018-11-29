<?php

namespace ChristianLuis\Algolia\Core;

use OxidEsales\Eshop\Core\DatabaseProvider;

class Events
{
    public static function addMissingFieldsOnUpdate()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);

        $tableFields = [
            'oxattribute' => [
                'ALGOLIASEARCHABLE' => "TINYINT(1) NOT NULL DEFAULT 1",
                'ALGOLIAFILTERABLE' => "TINYINT(1) NOT NULL DEFAULT 1",
            ]
        ];

        foreach ($tableFields as $tableName => $fieldArray) {
            foreach ($fieldArray as $fieldName => $fieldTypeSql) {
                if (!$dbMetaDataHandler->fieldExists($fieldName, $tableName)) {
                    DatabaseProvider::getDb()->execute("
                      ALTER TABLE {$tableName} 
                      ADD COLUMN {$fieldName} {$fieldTypeSql} ;");
                }
            }
        }
    }

    public static function onActivate()
    {
        self::addMissingFieldsOnUpdate();
    }
}
