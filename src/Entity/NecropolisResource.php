<?php

namespace Necropolis\Entity;

use DateTime;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\User;

/**
 * @Entity
 */
class NecropolisResource extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $title;

    /**
     * @Column(type="boolean")
     */
    protected $isPublic = true;

    /**
     * @Column(type="datetime")
     */
    protected $created;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @Column(type="datetime")
     */
    protected $deleted;

    /**
     * @ManyToOne(targetEntity="Omeka\Entity\User")
     * @JoinColumn(onDelete="SET NULL")
     */
    protected $deleter;

    /**
     * @Column(type="string")
     */
    protected $resourceType;

    /**
     * @Column(type="json_array")
     */
    protected $representation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setIsPublic($isPublic)
    {
        $this->isPublic = (bool) $isPublic;
    }

    public function isPublic()
    {
        return (bool) $this->isPublic;
    }

    public function setCreated(DateTime $dateTime)
    {
        $this->created = $dateTime;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setModified(DateTime $dateTime)
    {
        $this->modified = $dateTime;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setDeleted(DateTime $dateTime)
    {
        $this->deleted = $dateTime;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleter(User $deleter = null)
    {
        $this->deleter = $deleter;
    }

    public function getDeleter()
    {
        return $this->deleter;
    }

    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
    }

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function setRepresentation($representation)
    {
        $this->representation = $representation;
    }

    public function getRepresentation()
    {
        return $this->representation;
    }
}
