<?php

namespace ChristianLuis\Algolia\Core;

use OxidEsales\Eshop\Application\Model\Attribute;
use OxidEsales\Eshop\Core\Registry;

class AlgoliaApi
{
    protected $client;
    protected $res;
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

    public function getResultFromAlgolia($indexName, $oxidSorting, $query = '*', $searchParameters = [], $catId = null, $sessionFilter = [])
    {
        $index = $this->getClient()->initIndex(Registry::get(AlgoliaApi::class)->getIndexName($indexName, $oxidSorting));
        $searchDefaultParameters = [
            'attributesToRetrieve' => [
                'objectID',
            ],
            'facets' => '*',
            'attributesToHighlight' => [],
            'distinct' => 1,
            'page' => 0,
            'hitsPerPage' => 10,
        ];


        if (count($sessionFilter)) {
            $filterArray = $this->transformSessionFilterToAlgoliaFacets($sessionFilter);
        }

        if ($catId) {
            $searchDefaultParameters['filters'] = $this->translateFiltersToAlgoliaFilters(['categories:' . $catId]);
        }

        if (!empty($filterArray)) {
            $searchDefaultParameters['facetFilters'] = $filterArray;
        }

        $searchParameters = array_merge($searchDefaultParameters, $searchParameters);

        $this->res = $index->search($query, $searchParameters);

        $this->res['articleIds'] = array_map(function ($value) {
            return $value['objectID'];
        }, $this->res['hits']);

        return $this->res;
    }

    public function getLastResultSet()
    {
        return $this->res;
    }

    protected function transformSessionFilterToAlgoliaFacets($sessionFilter)
    {
        $filterArray = [];
        foreach ($sessionFilter as $attributeId => $filterValue) {
            if (empty($filterValue)) {
                continue;
            }
            $attribute = oxNew(Attribute::class);
            if (!$attribute->load($attributeId)) {
                continue;
            }
            $filterArray[] = "attributes." . $attribute->oxattribute__oxtitle->value . ":" . $filterValue . "";
        }

        return $filterArray;
    }

    protected function translateFiltersToAlgoliaFilters($filterArray)
    {
        return implode(' AND ', $filterArray);
    }
}
