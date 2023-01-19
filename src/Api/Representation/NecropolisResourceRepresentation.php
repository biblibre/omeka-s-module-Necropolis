<?php

namespace Necropolis\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class NecropolisResourceRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-Necropolis:NecropolisResource';
    }

    public function getJsonLd()
    {
        return [
            'o:title' => $this->title(),
            'o:is_public' => $this->isPublic(),
            'o:created' => $this->created(),
            'o:modified' => $this->modified(),
            'o:deleted' => $this->deleted(),
            'o:deleter' => $this->deleter()->getReference(),
            'o:resource_type' => $this->resourceType(),
            'o:representation' => $this->representation(),
        ];
    }

    public function title()
    {
        return $this->resource->getTitle();
    }

    public function isPublic()
    {
        return $this->resource->isPublic();
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function modified()
    {
        return $this->resource->getModified();
    }

    public function deleted()
    {
        return $this->resource->getDeleted();
    }

    public function deleter()
    {
        return $this->getAdapter('users')->getRepresentation($this->resource->getDeleter());
    }

    public function resourceType()
    {
        return $this->resource->getResourceType();
    }

    public function representation()
    {
        return $this->resource->getRepresentation();
    }

    public function displayTitle()
    {
        $title = $this->title();
        if (!isset($title)) {
            $title = $this->getServiceLocator()->get('MvcTranslator')->translate('[Untitled]');
        }

        return $title;
    }
}
