<?php

namespace ChristianLuis\Algolia\Application\Model;

class AttributeList extends AttributeList_parent
{
    public function loadAllAlgoliaAttributes()
    {
        $viewName = $this->getBaseObject()->getViewName();
        $activeSnippet = $this->getBaseObject()->getSqlActiveSnippet() ? $this->getBaseObject()->getSqlActiveSnippet() : "TRUE";
        $sql = "SELECT * FROM {$viewName} 
                WHERE ({$viewName}.ALGOLIASEARCHABLE = 1 
                   OR {$viewName}.ALGOLIAFILTERABLE = 1)
                   AND {$activeSnippet}";
        $this->selectString($sql);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function loadFromAlgoliaFacets($facets, $categoryId = null, $sessionFilter = null)
    {
        foreach ($facets as $key => $values) {
            $key = str_replace('attributes.', '', $key);
            $attribute = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);

            if (!$attribute->loadByTitle($key) || !$attribute->oxattribute__algoliafilterable->value) {
                continue;
            }

            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
            if ($categoryId && isset($sessionFilter[$categoryId][$iLang][$attribute->getId()])) {
                $attribute->setActiveValue($sessionFilter[$categoryId][$iLang][$attribute->getId()]);
            }

            $attribute->setTitle($attribute->oxattribute__oxtitle->value);


            foreach ($values as $attributeValue => $articleCount) {
                $attribute->addValue($attributeValue);
            }

            $this->add($attribute);
        }
    }
}
