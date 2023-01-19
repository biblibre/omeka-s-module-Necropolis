<?php

namespace Necropolis\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel;

        $items = $this->api()->search('necropolis_resources', [
            'resource_type' => 'Omeka\Entity\Item',
            'per_page' => 5,
            'page' => 1,
            'sort_by' => 'deleted',
            'sort_order' => 'desc',
        ])->getContent();

        $item_sets = $this->api()->search('necropolis_resources', [
            'resource_type' => 'Omeka\Entity\ItemSet',
            'per_page' => 5,
            'page' => 1,
            'sort_by' => 'deleted',
            'sort_order' => 'desc',
        ])->getContent();

        $medias = $this->api()->search('necropolis_resources', [
            'resource_type' => 'Omeka\Entity\Media',
            'per_page' => 5,
            'page' => 1,
            'sort_by' => 'deleted',
            'sort_order' => 'desc',
        ])->getContent();

        $view->setVariable('items', $items);
        $view->setVariable('item_sets', $item_sets);
        $view->setVariable('medias', $medias);

        return $view;
    }
}
