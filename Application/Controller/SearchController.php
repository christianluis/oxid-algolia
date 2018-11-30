<?php

namespace ChristianLuis\Algolia\Application\Controller;

use ChristianLuis\Algolia\Core\AlgoliaApi;
use OxidEsales\Eshop\Application\Model\AttributeList;
use OxidEsales\Eshop\Core\Registry;

class SearchController extends SearchController_parent
{
    public function init()
    {
        if ($this->getFncName() == 'executefilter') {
            $baseLanguageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
            // store this into session
            $attributeFilter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('attrfilter', true);
            $activeCategory = 'search';

            if (!empty($attributeFilter)) {
                $sessionFilter = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter');
                //fix for #2904 - if language will be changed attributes of this category will be deleted from session
                //and new filters for active language set.
                $sessionFilter[$activeCategory] = null;
                $sessionFilter[$activeCategory][$baseLanguageId] = $attributeFilter;
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('session_attrfilter', $sessionFilter);
            }
        }

        parent::init();
    }

    public function getAttributes()
    {
        $this->_aAttributes = false;

        if (($res = Registry::get(AlgoliaApi::class)->getLastResultSet())) {
            $attributeList = oxNew(AttributeList::class);
            $attributeList->loadFromAlgoliaFacets($res['facets'], 'search', \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter'));

            if ($attributeList->count()) {
                $this->_aAttributes = $attributeList;
            }
        }

        return $this->_aAttributes;
    }

    /**
     * Needs to exists
     */
    public function executefilter()
    {
    }
}
