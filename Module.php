<?php

namespace Necropolis;

use Composer\Semver\Comparator;
use DateTime;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Omeka\Module\AbstractModule;
use PDO;

class Module extends AbstractModule
{
    protected array $pendingDeletions = [];

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

    public function upgrade($oldVersion, $newVersion, ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        if (Comparator::lessThan($oldVersion, '0.3.0')) {
            $connection->executeStatement(<<<'SQL'
                DELETE FROM necropolis_resource
                WHERE EXISTS (SELECT * FROM resource WHERE resource.id = necropolis_resource.id)
            SQL);
        }
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        foreach (['Omeka\Entity\Item', 'Omeka\Entity\ItemSet', 'Omeka\Entity\Media'] as $identifier) {
            $sharedEventManager->attach($identifier, 'entity.remove.pre', [$this, 'onResourceRemovePre']);
            $sharedEventManager->attach($identifier, 'entity.remove.post', [$this, 'onResourceRemovePost']);
        }
    }

    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }

    public function onResourceRemovePre(Event $event)
    {
        $services = $this->getServiceLocator();
        $apiAdapterManager = $services->get('Omeka\ApiAdapterManager');
        $authenticationService = $services->get('Omeka\AuthenticationService');

        $resource = $event->getTarget();
        $apiAdapter = $apiAdapterManager->get($resource->getResourceName());
        $representation = $apiAdapter->getRepresentation($resource);
        $deleter = $authenticationService->getIdentity();

        $id = spl_object_id($resource);
        $this->pendingDeletions[$id] = [
            'id' => $resource->getId(),
            'title' => $resource->getTitle(),
            'is_public' => $resource->isPublic(),
            'created' => $resource->getCreated(),
            'modified' => $resource->getModified(),
            'deleter_id' => $deleter ? $deleter->getId() : null,
            'resource_type' => $resource->getResourceId(),
            'representation' => json_encode($representation),
        ];
    }

    public function onResourceRemovePost(Event $event)
    {
        $services = $this->getServiceLocator();

        $resource = $event->getTarget();
        $id = spl_object_id($resource);
        if (!array_key_exists($id, $this->pendingDeletions)) {
            $logger = $services->get('Omeka\Logger');
            $logger->warn('Necropolis: failed to retrieve information about the deleted resource');
            return;
        }

        $connection = $services->get('Omeka\Connection');

        $data = $this->pendingDeletions[$id];
        $sql = 'INSERT INTO necropolis_resource'
            . ' (id, title, is_public, created, modified, deleted, deleter_id, resource_type, representation) VALUES'
            . ' (:id, :title, :is_public, :created, :modified, :deleted, :deleter_id, :resource_type, :representation)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('id', $data['id'], PDO::PARAM_INT);
        $stmt->bindValue('title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue('is_public', $data['is_public'], PDO::PARAM_BOOL);
        $stmt->bindValue('created', $data['created'], 'datetime');
        $stmt->bindValue('modified', $data['modified'], 'datetime');
        $stmt->bindValue('deleted', new DateTime(), 'datetime');
        if ($data['deleter_id']) {
            $stmt->bindValue('deleter_id', $data['deleter_id'], PDO::PARAM_INT);
        } else {
            $stmt->bindValue('deleter_id', null, PDO::PARAM_NULL);
        }
        $stmt->bindValue('resource_type', $data['resource_type'], PDO::PARAM_STR);
        $stmt->bindValue('representation', $data['representation'], PDO::PARAM_STR);

        if (method_exists($stmt, 'executeStatement')) {
            $stmt->executeStatement();
        } else {
            $stmt->execute();
        }

        unset($this->pendingDeletions[$id]);
    }
}
