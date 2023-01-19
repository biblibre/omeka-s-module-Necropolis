<?php

namespace Necropolis\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class MediaController extends AbstractActionController
{
    public function browseAction()
    {
        $this->setBrowseDefaults('deleted');
        $params = $this->params()->fromQuery();
        $params['resource_type'] = 'Omeka\Entity\Media';

        $response = $this->api()->search('necropolis_resources', $params);
        $this->paginator($response->getTotalResults());

        $medias = $response->getContent();

        $view = new ViewModel();
        $view->setVariable('medias', $medias);

        return $view;
    }
}
