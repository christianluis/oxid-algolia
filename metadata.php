<?php
/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2018
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'clalgolia',
    'title'        => 'Algolia',
    'description'  => array(
        'de' => 'Modul für Algolia',
        'en' => 'Module for Algolia',
    ),
    'thumbnail'    => 'logo.svg',
    'version'      => '1.0.0',
    'author'       => 'Christian Luis',
    'url'          => '-',
    'email'        => 'christian.luis@ninnia.de',
    'extend'       => array(
        \OxidEsales\Eshop\Application\Model\Article::class => \ChristianLuis\Algolia\Application\Model\Article::class,
        \OxidEsales\Eshop\Application\Model\ArticleList::class => \ChristianLuis\Algolia\Application\Model\ArticleList::class,
        \OxidEsales\Eshop\Application\Model\AttributeList::class => \ChristianLuis\Algolia\Application\Model\AttributeList::class,
        \OxidEsales\Eshop\Application\Model\Search::class => \ChristianLuis\Algolia\Application\Model\Search::class,
    ),
    'controllers' => array(
        \ChristianLuis\Algolia\Core\Exporter::class => \ChristianLuis\Algolia\Core\Exporter::class,
        \ChristianLuis\Algolia\Core\AlgoliaApi::class => \ChristianLuis\Algolia\Core\AlgoliaApi::class,
    ),
    'events'       => array(
        'onActivate'   => '\ChristianLuis\Algolia\Core\Events::onActivate',
    ),
    'templates' => array(),
    'blocks' => array(
        array('template' => 'attribute_main.tpl', 'block'=>'admin_attribute_main_form', 'file'=>'/Application/views/admin/blocks/admin_attribute_main_form.tpl'),
    ),
    'settings' => array(
        array('group' => 'clalgolia_api', 'name' => 'clalgolia_api_activate', 'type' => 'bool', 'value' => 'false'),
        array('group' => 'clalgolia_api', 'name' => 'clalgolia_api_appid', 'type' => 'str', 'value' => ''),
        array('group' => 'clalgolia_api', 'name' => 'clalgolia_api_appkey', 'type' => 'str', 'value' => ''),
    )
);
