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
            $searchParameters = [
                'filters' => "categories:'{$sCatId}'",
                'page' => $this->_aSqlLimit[0] / $this->_aSqlLimit[1],
                'hitsPerPage' => $this->_aSqlLimit[1],
            ];
            $algoriaResult = Registry::get(AlgoliaApi::class)->getResultFromAlgolia('Articles', $this->_sCustomSorting, '*', $searchParameters);

            $this->setSqlLimit(0, 0);
            $this->loadIds($algoriaResult['articleIds']);
            $this->sortByIds($algoriaResult['articleIds']);
        } catch (\Algolia\AlgoliaSearch\Exceptions\NotFoundException $ex) {
            $displayEx = oxNew(DisplayError::class);
            $displayEx->setMessage('ERROR_NO_INDEX_FOUND');
            $oxUtilsView = Registry::getUtilsView();
            $oxUtilsView->addErrorToDisplay($displayEx);
        }

        return $algoriaResult['nbHits'];
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
