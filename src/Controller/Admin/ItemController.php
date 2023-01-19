<?php

namespace Necropolis\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ItemController extends AbstractActionController
{
    public function browseAction()
    {
        $this->setBrowseDefaults('deleted');
        $params = $this->params()->fromQuery();
        $params['resource_type'] = 'Omeka\Entity\Item';

        $response = $this->api()->search('necropolis_resources', $params);
        $this->paginator($response->getTotalResults());

        $items = $response->getContent();

        $view = new ViewModel();
        $view->setVariable('items', $items);

        return $view;
    }
}
