<?php


namespace Sinervis\FileUploaderBundle\EventSubscriber;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Sinervis\FileUploaderBundle\Entity\SvFile;
use Sinervis\FileUploaderBundle\Util\MetadataReader;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SinervisFileSubscriber implements EventSubscriber
{
    private $em;
    private $reader;

    public function __construct(EntityManagerInterface $em, MetadataReader $reader)
    {
        $this->em = $em;
        $this->reader = $reader;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush
        ];
    }


    public function postFlush(PostFlushEventArgs $args): void
    {
        $query = $this->em->createQuery(
                "DELETE SinervisFileUploaderBundle:SvFile sf
                WHERE sf.softDelete = :softDelete"
            )
            ->setParameter('softDelete', true);
        $query->execute();
    }


    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($this->reader->isUploadable(get_class($entity))) {
            $this->postEvent($entity, 'postPersist');
        }
    }


    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($this->reader->isUploadable(get_class($entity))) {
            $this->postEvent($entity, 'postUpdate');
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($this->reader->isUploadable(get_class($entity))) {
            $this->postEvent($entity, 'postRemove');

            $this->em->createQuery(
                "DELETE SinervisFileUploaderBundle:SvFile sf
                WHERE sf.ownerClass = :ownerClass
                AND sf.ownerId = :ownerId"
            )
                ->setParameter('ownerClass', get_class($entity))
                ->setParameter('ownerId', $entity->getId())
                ->execute();
        }
    }


    public function postEvent($entity, $operation): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $uploadableFields = $this->reader->getUploadableFields(get_class($entity));
        foreach ($uploadableFields as $uploadableField) {
            $propertyName = $uploadableField->getName();
            $svFile = $propertyAccessor->getValue($entity, $propertyName);
            if ($svFile instanceof \Traversable) {
                $this->handleFiles($entity, $propertyName, $svFile, $operation);
            } else {
                $this->handleFile($entity, $propertyName, $svFile, $operation);
            }
        }
    }

    private function handleFiles($ownerEntity, $propertyName, $svFiles = [], $operation): void
    {
        $this->removeDetachedFiles($ownerEntity, $svFiles);
        foreach ($svFiles as $svFile) {
            $this->handleFile($ownerEntity, $propertyName, $svFile, $operation);
        }
    }

    private function handleFile($ownerEntity, $propertyName, ?SvFile $svFile = null, $operation): void
    {
        if ($svFile) {
            $this->removePreviousFile($svFile);
            $this->removeRecordFromTemporaryTable($svFile->getName());

            if ($operation === 'postRemove') {
                $this->removeSvFile($svFile);
            } else {
                $fileName = $svFile->getName();
                $this->updateMissingInfoInSvFileTable($ownerEntity, $propertyName, $fileName);
            }
        }

        if ($operation === 'postRemove') {
            $this->removeAllSvFilesFromEntityBeingDeleted($ownerEntity);
        }
    }

    private function removeSvFile($svFile): void
    {
        if ($svFile && $svFile->getUri() && $svFile->getName()) {
            $fullFileName = $svFile->getUri() . '/' . $svFile->getName();
            if (file_exists($fullFileName)) {
                unlink($fullFileName);
            }
        }
    }

    private function removeAllSvFilesFromEntityBeingDeleted($ownerEntity): void
    {
        $sql = "SELECT * FROM sv_file WHERE owner_class = :ownerClass AND owner_id = :ownerId";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute([
            'ownerClass' => get_class($ownerEntity),
            'ownerId' => $ownerEntity->getId()
        ]);

        while ($rec = $stmt->fetch()) {
            $fileName = $rec['name'];
            $uri = $rec['uri'];
            $fullFileName = $uri . '/' . $fileName;

            if (file_exists($fullFileName)) {
                unlink($fullFileName);
            }
        }
    }

    /**
     * When a file gets replace by a new one, this method removes the old file.
     */
    private function removePreviousFile(SvFile $svFile): void
    {
        $fileToBeRemoved = $svFile->getOldFileNameToBeDeleted();
        $this->unlinkFile($fileToBeRemoved);
    }

    /**
     * This method removes detached files in a ManyToMany relationship.
     */
    private function removeDetachedFiles($ownerEntity, $svFiles): void
    {
        $sql = "SELECT * FROM sv_file WHERE owner_class = :ownerClass AND owner_id = :ownerId";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute([
            'ownerClass' => get_class($ownerEntity),
            'ownerId' => $ownerEntity->getId()
        ]);

        $validSvFileIds = [];
        if ($svFiles && $svFiles instanceof \Traversable) {
            $validSvFileIds = $svFiles->map(function (SvFile $svFile) {
                return $svFile->getId();
            })->toArray();
        }

        while ($rec = $stmt->fetch()) {
            if (in_array($rec['id'], $validSvFileIds) === false) {
                $fileName = $rec['name'];
                $uri = $rec['uri'];
                $fullFileName = $uri . '/' . $fileName;

                $this->unlinkFile($fullFileName);

                $deleteSql = "DELETE FROM sv_file WHERE id = :id";
                $deletedStmt = $this->em->getConnection()->prepare($deleteSql);
                $deletedStmt->execute(['id' => $rec['id']]);
            }
        }
    }

    private function removeRecordFromTemporaryTable($fileName): void
    {
        $sql = "DELETE FROM sv_tmp_file WHERE file_name = :fileName";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute(['fileName' => $fileName]);
    }

    private function updateMissingInfoInSvFileTable($ownerEntity, $propertyName, $fileName): void
    {
        $sql = "UPDATE sv_file SET owner_class = :ownerClass, owner_property = :ownerProperty, owner_id = :ownerId WHERE name = :fileName";
        $params = [
            'ownerClass' => get_class($ownerEntity),
            'ownerProperty' => $propertyName,
            'ownerId' => $ownerEntity->getId(),
            'fileName' => $fileName
        ];
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($params);
    }

    private function unlinkFile($fullFileName)
    {
        if (file_exists($fullFileName)) {
            unlink($fullFileName);
        }
    }
}