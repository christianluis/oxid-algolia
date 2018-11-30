<?php

namespace ChristianLuis\Algolia\Core;

use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Application\Model\AttributeList;
use OxidEsales\Eshop\Core\Registry;

class Exporter
{
    protected $client;

    public function __construct()
    {
        $this->client = Registry::get(AlgoliaApi::class)->getClient();
    }

    public function execute()
    {
        $this->log("[#] Execute Exporter");
        $this->log("[i] Current shop ID: " . Registry::getConfig()->getShopId());
        $this->log("[i] Current Language: " . Registry::getLang()->getLanguageAbbr());

        $articlesPayload = $this->transformArticles($this->getAllArticles());
        $this->sendToAlgolia(Registry::get(AlgoliaApi::class)->getIndexName("Articles"), $articlesPayload);
    }

    protected function getAllArticles()
    {
        $articleList = oxNew(ArticleList::class);
        $articleList->loadAllAlgoliaExportArticles();

        return $articleList;
    }

    protected function getAllAttributes()
    {
        $attributeList = oxNew(AttributeList::class);
        $attributeList->loadAllAlgoliaAttributes();

        return $attributeList;
    }

    protected function transformArticles($articles)
    {
        $this->log("[i] Count of articles to export: " . count($articles));
        $payload = [];
        foreach ($articles as $article) {
            $articleAttributes = [];
            foreach ($article->getAttributes() as $attribute) {
                $articleAttributes[] = [
                    $attribute->oxattribute__oxtitle->rawValue => $attribute->oxattribute__oxvalue->rawValue
                ];
            }

            $payload[] = array_filter([
                "objectID" => $article->getId(),
                "oxparentid" => $article->getAlgoliaDistinctIdentifier(),
                "oxartnum" => $article->oxarticles__oxartnum->rawValue,
                "oxean" => $article->oxarticles__oxean->rawValue,
                "oxtitle" => $article->oxarticles__oxtitle->rawValue,
                "oxshortdesc" => $article->oxarticles__oxshortdesc->rawValue,
                "oxprice" => (float)$article->oxarticles__oxprice->value,
                "oxvarminprice" => (float)$article->oxarticles__oxvarminprice->value,
                "oxstock" => (int)$article->oxarticles__oxstock->value,
                "oxsearchkeys" => array_map('trim', explode(",", $article->oxarticles__oxsearchkeys->rawValue)),
                "oxvendorid" => $article->oxarticles__oxvendorid->value,
                "oxmanufacturerid" => $article->oxarticles__oxvendorid->value,
                "oxsoldamount" => (int)$article->oxarticles__oxsoldamount->value,
                "oxrating" => (float)$article->oxarticles__oxrating->value,
                "oxtimestamp" => $article->oxarticles__oxtimestamp->value,
                "categories" => $article->getCategoryIds(),
                "attributes" => $articleAttributes,
            ], function ($value) {
                if (is_string($value) && empty($value)) {
                    return false;
                }
                if (is_array($value) && count($value) == 0) {
                    return false;
                }

                return true;
            });
        }

        return $payload;
    }

    protected function sendToAlgolia($indexName, $payload)
    {
        $registeredSortCols = Registry::getConfig()->getConfigParam('aSortCols');
        $sortMethods = [];
        foreach ($registeredSortCols as $col) {
            $sortMethods[$indexName . '_' . $col . '_desc'] = "desc({$col})";
            $sortMethods[$indexName . '_' . $col . '_asc'] = "asc({$col})";
        }

        $attributesForFaceting = ['filterOnly(categories)'];
        foreach ($this->getAllAttributes() as $attribute) {
            if ($attribute->oxattribute__algoliasearchable->value == 1) {
                $attributesForFaceting[] = "searchable(attributes.{$attribute->oxattribute__oxtitle->rawValue})";
            } elseif ($attribute->oxattribute__algoliafilterable->value == 1) {
                $attributesForFaceting[] = "filterOnly(attributes.{$attribute->oxattribute__oxtitle->rawValue})";
            }
        }

        $index = $this->client->initIndex($indexName);
        $index->setSettings([
            'replicas' => array_keys($sortMethods),
            'attributesForFaceting' => $attributesForFaceting,
            'attributeForDistinct' => 'oxparentid',
        ]);

        foreach ($sortMethods as $key => $value) {
            $$key = $this->client->initIndex($key);
            $$key->setSettings([
                "ranking" => [
                    $value,
                ],
                'attributesForFaceting' => $attributesForFaceting,
                'attributeForDistinct' => 'oxparentid',
            ]);
        }
        $index->saveObjects($payload);
    }

    protected function log($message, $context = [])
    {
        echo $message;

        if (count($context)) {
            echo ' (' . print_r($context, true) . ')';
        }

        echo PHP_EOL;
    }
}
