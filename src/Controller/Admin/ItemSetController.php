<?php

namespace Necropolis\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ItemSetController extends AbstractActionController
{
    public function browseAction()
    {
        $this->setBrowseDefaults('deleted');
        $params = $this->params()->fromQuery();
        $params['resource_type'] = 'Omeka\Entity\ItemSet';

        $response = $this->api()->search('necropolis_resources', $params);
        $this->paginator($response->getTotalResults());

        $itemSets = $response->getContent();

        $view = new ViewModel();
        $view->setVariable('itemSets', $itemSets);

        return $view;
    }
}
