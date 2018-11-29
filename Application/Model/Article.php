<?php

namespace ChristianLuis\Algolia\Application\Model;

class Article extends Article_parent
{
    public function getAlgoliaDistinctIdentifier()
    {
        $id = $this->oxarticles__oxparentid->value ? $this->oxarticles__oxparentid->value : $this->getId();
        return $id . "_parent";
    }
}
