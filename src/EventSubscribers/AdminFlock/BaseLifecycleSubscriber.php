<?php

namespace App\EventSubscribers\AdminFlock;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;

abstract class BaseLifecycleSubscriber implements EventSubscriber
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * BaseLifecycleSubscriber constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [];
    }

    /**
     * @param string $event
     * @param LifecycleEventArgs $args
     * @return bool
     * @throws ReflectionException
     */
    private function dispatch(string $event, LifecycleEventArgs $args): bool
    {
        if (!in_array($event, $this->getSubscribedEvents())) {
            return false;
        }

        $entity = $args->getObject();

        $methodToExecute = 'on' . (new ReflectionClass($entity))->getShortName() . ucfirst($event);

        if (method_exists($this, $methodToExecute)) {
            $this->$methodToExecute($entity, $args);
            return true;
        }

        return false;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->dispatch(__FUNCTION__, $args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($this->dispatch(__FUNCTION__, $args)) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($this->dispatch(__FUNCTION__, $args)) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $this->dispatch(__FUNCTION__, $args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->dispatch(__FUNCTION__, $args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->dispatch(__FUNCTION__, $args);
    }
}