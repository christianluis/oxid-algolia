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

        if (!is_array($this->_aSqlLimit) || count($this->_aSqlLimit) !== 2
            || $this->_aSqlLimit[1] < 1) {
            $this->_aSqlLimit = [
                0,
                $this->getConfig()->getConfigParam('iNrofCatArticles'),
            ];
        }

        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sessionFilter = null;
        if ($aSessionFilter && isset($aSessionFilter[$sCatId][$iLang])) {
            $sessionFilter = $aSessionFilter[$sCatId][$iLang];
//            $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $searchParameters = [
            'page' => $this->_aSqlLimit[0] / $this->_aSqlLimit[1],
            'hitsPerPage' => $this->_aSqlLimit[1],
        ];

        try {
            $algoriaResult = Registry::get(AlgoliaApi::class)->getResultFromAlgolia('Articles', $this->_sCustomSorting, '*', $searchParameters, $sCatId, $sessionFilter);

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
