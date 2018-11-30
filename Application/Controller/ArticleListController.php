<?php

namespace ChristianLuis\Algolia\Application\Controller;

use ChristianLuis\Algolia\Core\AlgoliaApi;
use OxidEsales\Eshop\Application\Model\AttributeList;
use OxidEsales\Eshop\Core\Registry;

class ArticleListController extends ArticleListController_parent
{
    public function getAttributes()
    {
        $this->_aAttributes = false;

        if (($res = Registry::get(AlgoliaApi::class)->getLastResultSet())) {
            $attributeList = oxNew(AttributeList::class);
            $attributeList->loadFromAlgoliaFacets($res['facets'], $this->getActiveCategory()->getId(), \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter'));

            if ($attributeList->count()) {
                $this->_aAttributes = $attributeList;
            }
        }

        return $this->_aAttributes;
    }
}
