<?php

namespace ChristianLuis\Algolia\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;

class Attribute extends Attribute_parent
{
    public function loadByTitle($title)
    {
        $viewName = $this->getViewName();
        $sql = "SELECT OXID FROM {$viewName} WHERE OXTITLE = ?";
        $id = DatabaseProvider::getDb()->getOne($sql, [$title]);

        if (!$id) {
            return false;
        }

        return $this->load($id);
    }
}
