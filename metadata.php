<?php

$sMetadataVersion = '2.0';

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
