<?php


namespace Sinervis\FileUploaderBundle\Service;


use Sinervis\FileUploaderBundle\Annotations\SinervisUploadableField;
use Sinervis\FileUploaderBundle\Entity\SvFile;
use Sinervis\FileUploaderBundle\Util\MetadataReader;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SinervisDownloadHelper
{
    private $metadataReader;
    private $authChecker;
    private $tokenStorage;

    public function __construct(MetadataReader $metadataReader, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage)
    {
        $this->metadataReader = $metadataReader;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function isPermissionGranted(?SvFile $svFile): bool
    {
        if (!$svFile) {
            return false;
        }

        $uploadableFieldPropAnnotation = $this->metadataReader->getUploadableFieldPropertyAnnotation(
            $svFile->getOwnerClass(), $svFile->getOwnerProperty(), SinervisUploadableField::class);

        if ($uploadableFieldPropAnnotation) {
            $permissions = $uploadableFieldPropAnnotation->getPermissions();
            if (!empty($permissions)) {
                $voter = $permissions['voter'] ?? null;
                if ($voter && $this->authChecker->isGranted($voter, $svFile)) {
                    return true;
                }

                $permittedRoles = $permissions['roles'] ?? [];
                if (!empty($permittedRoles)) {
                    $userRoles = $this->tokenStorage->getToken()->getUser()->getRoles();
                    $commonRoles = array_intersect($userRoles, $permittedRoles);
                    return count($commonRoles) > 0;
                }
            }
        }

        return true;
    }
}