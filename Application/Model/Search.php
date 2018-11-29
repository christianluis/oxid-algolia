<?php

namespace ChristianLuis\Algolia\Application\Model;

use ChristianLuis\Algolia\Core\AlgoliaApi;
use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Core\DisplayError;
use OxidEsales\Eshop\Core\Registry;

class Search extends Search_parent
{
    public $res;

    public function getSearchArticles($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false, $sSortBy = false)
    {
        if (!Registry::getConfig()->getConfigParam('clalgolia_api_activate')) {
            return parent::getSearchArticles($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer, $sSortBy);
        }

        $this->iActPage = (int) Registry::get(\OxidEsales\Eshop\Core\Request::class)->getRequestEscapedParameter('pgNr');
        $this->iActPage = ($this->iActPage < 0) ? 0 : $this->iActPage;

        $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

        $articleList = oxNew(ArticleList::class);

        try {
            $searchParameters = [
                'page' => $this->iActPage,
                'hitsPerPage' => $iNrofCatArticles
            ];
            $this->res = Registry::get(AlgoliaApi::class)->getResultFromAlgolia('Articles', $sSortBy, $sSearchParamForQuery, $searchParameters);

            $articleList->loadIds($this->res['articleIds']);
            $articleList->sortByIds($this->res['articleIds']);
        } catch (\Algolia\AlgoliaSearch\Exceptions\NotFoundException $ex) {
            $displayEx = oxNew(DisplayError::class);
            $displayEx->setMessage('ERROR_NO_INDEX_FOUND');
            $oxUtilsView = Registry::getUtilsView();
            $oxUtilsView->addErrorToDisplay($displayEx);
        }

        return $articleList;
    }

    public function getSearchArticleCount($sSearchParamForQuery = false, $sInitialSearchCat = false, $sInitialSearchVendor = false, $sInitialSearchManufacturer = false)
    {
        if (!Registry::getConfig()->getConfigParam('clalgolia_api_activate')) {
            return parent::getSearchArticleCount($sSearchParamForQuery, $sInitialSearchCat, $sInitialSearchVendor, $sInitialSearchManufacturer);
        }
        return (int)$this->res['nbHits'];
    }
}
