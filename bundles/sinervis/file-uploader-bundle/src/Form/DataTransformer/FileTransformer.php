<?php

namespace Sinervis\FileUploaderBundle\Form\DataTransformer;

use Sinervis\FileUploaderBundle\Entity\SvFile;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class FileTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function transform($file): ?SvFile
    {
        if (is_null($file)) {
            return null;
        }

        return $file;
    }

    public function reverseTransform($svFile)
    {
        if($svFile && $svFile->isSoftDelete()) {
            return null;
        }

        // File object exists and no file name means the file is about to be deleted.
        // So return null instead of the SvFile object.
        if ($svFile && $svFile->getId() === null && $svFile->getName() === null) {
            return null;
        }

        $prevFileName = $this->getFullFileNameOfSvFileById($svFile);

        if ($svFile && $prevFileName !== null && $svFile->getName() !== $prevFileName) {
            $svFile->setOldFileNameToBeDeleted($svFile->getUri() .'/'. $prevFileName);
        }

        return $svFile;
    }

    private function getFullFileNameOfSvFileById($svFile)
    {
        $entityId = null;
        if ($svFile && $svFile->getId()) {
            $entityId = $svFile->getId();
        }

        if ($entityId) {
            $conn = $this->em->getConnection();
            $sql = "SELECT name FROM sv_file WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('id', $entityId);
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        return null;
    }

}