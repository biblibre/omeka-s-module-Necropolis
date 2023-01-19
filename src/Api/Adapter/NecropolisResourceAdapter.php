<?php

namespace Necropolis\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class NecropolisResourceAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id',
        'title',
        'created',
        'modified',
        'deleted',
    ];

    public function getResourceName()
    {
        return 'necropolis_resources';
    }

    public function getRepresentationClass()
    {
        return \Necropolis\Api\Representation\NecropolisResourceRepresentation::class;
    }

    public function getEntityClass()
    {
        return \Necropolis\Entity\NecropolisResource::class;
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        if (Request::CREATE !== $request->getOperation()) {
            return;
        }

        $resource = $request->getValue('o:resource');
        if (!$resource instanceof \Omeka\Entity\Resource) {
            return;
        }

        $services = $this->getServiceLocator();
        $apiAdapterManager = $services->get('Omeka\ApiAdapterManager');
        $authenticationService = $services->get('Omeka\AuthenticationService');
        $apiAdapter = $apiAdapterManager->get($resource->getResourceName());
        $representation = $apiAdapter->getRepresentation($resource);

        $entity->setId($resource->getId());
        $entity->setTitle($resource->getTitle());
        $entity->setIsPublic($resource->isPublic());
        $entity->setCreated($resource->getCreated());
        $entity->setModified($resource->getModified());
        $entity->setDeleted(new DateTime());
        $entity->setDeleter($authenticationService->getIdentity());
        $entity->setResourceType($resource->getResourceId());
        $entity->setRepresentation($representation);
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (!empty($query['resource_type'])) {
            $qb->andWhere($qb->expr()->eq(
                "omeka_root.resourceType",
                $this->createNamedParameter($qb, $query['resource_type']))
            );
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (!$entity->getId()) {
            $errorStore->addError('o:id', 'The id cannot be empty.');
        }
        if (!$entity->getResourceType()) {
            $errorStore->addError('o:resource_type', 'The resource type cannot be empty.');
        }
    }
}
