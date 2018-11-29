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

    public function getResultFromAlgolia($indexName, $oxidSorting, $query = '*', $searchParameters = [])
    {
        $index = $this->getClient()->initIndex(Registry::get(AlgoliaApi::class)->getIndexName($indexName, $oxidSorting));
        $searchDefaultParameters = [
            'attributesToRetrieve' => [
                'objectID',
            ],
            'attributesToHighlight' => [],
            'distinct' => 1,
            'page' => 0,
            'hitsPerPage' => 10,
        ];

        $searchParameters = array_merge($searchDefaultParameters, $searchParameters);

        $res = $index->search($query, $searchParameters);

        $res['articleIds'] = array_map(function ($value) {
            return $value['objectID'];
        }, $res['hits']);

        return $res;
    }
}
