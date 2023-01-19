<?php

namespace Necropolis;

use DateTime;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Necropolis\Entity\NecropolisResource;
use Omeka\Module\AbstractModule;

class Module extends AbstractModule
{
    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec("CREATE TABLE necropolis_resource (id INT NOT NULL, deleter_id INT DEFAULT NULL, title LONGTEXT DEFAULT NULL, is_public TINYINT(1) NOT NULL, created DATETIME NOT NULL, modified DATETIME DEFAULT NULL, deleted DATETIME NOT NULL, resource_type VARCHAR(255) NOT NULL, representation LONGTEXT NOT NULL COMMENT '(DC2Type:json_array)', INDEX IDX_E4CC5EA5EAEF1DFE (deleter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $connection->exec("ALTER TABLE necropolis_resource ADD CONSTRAINT FK_E4CC5EA5EAEF1DFE FOREIGN KEY (deleter_id) REFERENCES user (id) ON DELETE SET NULL");
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec("DROP TABLE IF EXISTS necropolis_resource");
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Entity\Item',
            'entity.remove.pre',
            [$this, 'onResourceRemove']
        );
        $sharedEventManager->attach(
            'Omeka\Entity\ItemSet',
            'entity.remove.pre',
            [$this, 'onResourceRemove']
        );
        $sharedEventManager->attach(
            'Omeka\Entity\Media',
            'entity.remove.pre',
            [$this, 'onResourceRemove']
        );
    }

    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }

    public function onResourceRemove(Event $event)
    {
        $services = $this->getServiceLocator();
        $logger = $services->get('Omeka\Logger');
        $em = $services->get('Omeka\EntityManager');
        $apiAdapterManager = $services->get('Omeka\ApiAdapterManager');
        $authenticationService = $services->get('Omeka\AuthenticationService');

        $resource = $event->getTarget();
        $apiAdapter = $apiAdapterManager->get($resource->getResourceName());
        $representation = $apiAdapter->getRepresentation($resource);

        $necropolisResource = new NecropolisResource();
        $necropolisResource->setId($resource->getId());
        $necropolisResource->setTitle($resource->getTitle());
        $necropolisResource->setIsPublic($resource->isPublic());
        $necropolisResource->setCreated($resource->getCreated());
        $necropolisResource->setModified($resource->getModified());
        $necropolisResource->setDeleted(new DateTime());
        $necropolisResource->setDeleter($authenticationService->getIdentity());
        $necropolisResource->setResourceType($resource->getResourceId());
        $necropolisResource->setRepresentation($representation);

        $em->persist($necropolisResource);
    }
}
