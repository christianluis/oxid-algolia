<?php

namespace ChristianLuis\Algolia\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class RssFeed extends RssFeed_parent
{
    public function loadCategoryArticles(\OxidEsales\Eshop\Application\Model\Category $oCat)
    {
        $sId = $oCat->getId();
        if (($this->_aChannel = $this->_loadFromCache(self::RSS_CATARTS . $sId))) {
            return;
        }

        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->setCustomSorting('oxtimestamp desc');
        $oArtList->loadCategoryArticles($oCat->getId(), null, $this->getConfig()->getConfigParam('iRssItemsCount'));

        $oLang = Registry::getLang();
        $this->_loadData(
            self::RSS_CATARTS . $sId,
            $this->getCategoryArticlesTitle($oCat),
            sprintf($oLang->translateString('S_CATEGORY_PRODUCTS', $oLang->getBaseLanguage()), $oCat->oxcategories__oxtitle->value),
            $this->_getArticleItems($oArtList),
            $this->getCategoryArticlesUrl($oCat),
            $oCat->getLink()
        );
    }

    public function loadSearchArticles($sSearch, $sCatId, $sVendorId, $sManufacturerId)
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('iNrofCatArticles', $oConfig->getConfigParam('iRssItemsCount'));

        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\Search::class)->getSearchArticles($sSearch, $sCatId, $sVendorId, $sManufacturerId, 'oxtimestamp desc');

        $this->_loadData(
        // dont use cache for search
            null,
            //self::RSS_SEARCHARTS.md5($sSearch.$sCatId.$sVendorId),
            $this->getSearchArticlesTitle($sSearch, $sCatId, $sVendorId, $sManufacturerId),
            $this->_getSearchParamsTranslation('SEARCH_FOR_PRODUCTS_CATEGORY_VENDOR_MANUFACTURER', getStr()->htmlspecialchars($sSearch), $sCatId, $sVendorId, $sManufacturerId),
            $this->_getArticleItems($oArtList),
            $this->getSearchArticlesUrl($sSearch, $sCatId, $sVendorId, $sManufacturerId),
            $this->_getShopUrl() . "cl=search&amp;" . $this->_getSearchParamsUrl($sSearch, $sCatId, $sVendorId, $sManufacturerId)
        );
    }


}
