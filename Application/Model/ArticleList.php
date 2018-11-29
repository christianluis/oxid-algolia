<?php

namespace ChristianLuis\Algolia\Application\Model;

use ChristianLuis\Algolia\Core\AlgoliaApi;
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Registry;

class ArticleList extends ArticleList_parent
{
    public function loadCategoryArticles($sCatId, $aSessionFilter, $iLimit = null)
    {
        if (!Registry::getConfig()->getConfigParam('clalgolia_api_activate')) {
            return parent::loadCategoryArticles($sCatId, $aSessionFilter, $iLimit);
        }
        try {
            $client = Registry::get(AlgoliaApi::class)->getClient();
            $index = $client->initIndex(Registry::get(AlgoliaApi::class)->getIndexName('Articles', $this->_sCustomSorting));

            $res = $index->search('*', [
                'filters' => "categories:'{$sCatId}'",
                'attributesToRetrieve' => [
                    'objectID',
                ],
                'attributesToHighlight' => [],
                'distinct' => 1,
                'page' => $this->_aSqlLimit[0] / $this->_aSqlLimit[1],
                'hitsPerPage' => $this->_aSqlLimit[1],
            ]);

            $articleIds = array_map(function ($value) {
                return $value['objectID'];
            }, $res['hits']);

            $this->setSqlLimit(0, 0);
            $this->loadIds($articleIds);
            $this->sortByIds($articleIds);
        } catch (\Algolia\AlgoliaSearch\Exceptions\NotFoundException $ex) {
            $displayEx = oxNew(DisplayError::class);
            $displayEx->setMessage('ERROR_NO_INDEX_FOUND');
            $oxUtilsView = Registry::getUtilsView();
            $oxUtilsView->addErrorToDisplay($displayEx);
        }

        return $res['nbHits'];
    }

    public function loadAllAlgoliaExportArticles()
    {
        $article = $this->getBaseObject();
        $viewName = $article->getViewName();
        $activeSnippet = $article->getSqlActiveSnippet();
        $sql = "SELECT * 
                FROM {$viewName}
                WHERE {$activeSnippet}";

        $this->selectString($sql);
    }
}
