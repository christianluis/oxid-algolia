<?php

namespace ChristianLuis\Algolia\Core;

use OxidEsales\Eshop\Core\Registry;

class AlgoliaApi
{
    protected $client;
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = \Algolia\AlgoliaSearch\SearchClient::create(
                Registry::getConfig()->getConfigParam('clalgolia_api_appid'),
                Registry::getConfig()->getConfigParam('clalgolia_api_appkey')
            );
        }
        return $this->client;
    }

    public function getIndexName($entityType, $sortBy = '', $shopId = null, $langId = null)
    {
        $shopId = $shopId ?? Registry::getConfig()->getShopId();
        $langId = $langId ?? Registry::getLang()->getBaseLanguage();

        if ($sortBy) {
            $sortBy = str_replace(" ", "_", $sortBy);
            $sortBy = str_replace("`", "", $sortBy);
            $sortBy = "_" . $sortBy;
        }

        return $entityType . "_" . $shopId . "_" . Registry::getLang()->getLanguageAbbr($langId) . $sortBy;
    }
}
