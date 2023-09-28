<?php

namespace Necropolis;

use DateTime;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Omeka\Module\AbstractModule;
use PDO;

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
        $apiAdapterManager = $services->get('Omeka\ApiAdapterManager');
        $authenticationService = $services->get('Omeka\AuthenticationService');
        $connection = $services->get('Omeka\Connection');

        $resource = $event->getTarget();
        $apiAdapter = $apiAdapterManager->get($resource->getResourceName());
        $representation = $apiAdapter->getRepresentation($resource);
        $deleter = $authenticationService->getIdentity();

        $sql = 'INSERT INTO necropolis_resource'
            . ' (id, title, is_public, created, modified, deleted, deleter_id, resource_type, representation) VALUES'
            . ' (:id, :title, :is_public, :created, :modified, :deleted, :deleter_id, :resource_type, :representation)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('id', $resource->getId(), PDO::PARAM_INT);
        $stmt->bindValue('title', $resource->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue('is_public', $resource->isPublic(), PDO::PARAM_BOOL);
        $stmt->bindValue('created', $resource->getCreated(), 'datetime');
        $stmt->bindValue('modified', $resource->getModified(), 'datetime');
        $stmt->bindValue('deleted', new DateTime(), 'datetime');
        if ($deleter) {
            $stmt->bindValue('deleter_id', $deleter->getId(), PDO::PARAM_INT);
        } else {
            $stmt->bindValue('deleter_id', null, PDO::PARAM_NULL);
        }
        $stmt->bindValue('resource_type', $resource->getResourceId(), PDO::PARAM_STR);
        $stmt->bindValue('representation', $representation, 'json_array');

        if (method_exists($stmt, 'executeStatement')) {
            $stmt->executeStatement();
        } else {
            $stmt->execute();
        }
    }
}
