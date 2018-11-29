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
}
