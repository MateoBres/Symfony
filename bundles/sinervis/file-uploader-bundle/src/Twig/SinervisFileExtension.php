<?php


namespace Sinervis\FileUploaderBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Sinervis\FileUploaderBundle\Entity\SvFile;
use Sinervis\FileUploaderBundle\Service\SinervisDownloadHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SinervisFileExtension extends AbstractExtension
{
    private $em;
    private $authChecker;
    private $router;
    private $downloadHelper;

    public function __construct(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker, RouterInterface $router, SinervisDownloadHelper $downloadHelper)
    {
        $this->em = $em;
        $this->authChecker = $authChecker;
        $this->router = $router;
        $this->downloadHelper = $downloadHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sinervis_file', [$this, 'sinervisFileLinkFunc'], ['is_safe' => ['html']]),
            new TwigFunction('sinervis_files', [$this, 'sinervisFilesLinkFunc'], ['is_safe' => ['html']]),
            new TwigFunction('sinervis_image_preview', [$this, 'sinervisImagePreviewLinkFunc'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('format_file_size', array($this, 'formatFileSizeFilter'))
        ];
    }

    public function sinervisFileLinkFunc(?SvFile $svFile)
    {
        if (!$svFile) {
            return;
        }

        if ($this->downloadHelper->isPermissionGranted($svFile)) {
            $link = $this->router->generate('sinervis_file_uploader_bundle_api_file_download', ['id' => $svFile->getId()]);
            return "<a href='$link'>".$svFile->getOriginalName()."</a>";
        }

        return $svFile->getOriginalName();
    }

    public function sinervisFilesLinkFunc($svFiles)
    {
        if (empty($svFiles)) {
            return;
        }

        $html = '';
        if ($this->downloadHelper->isPermissionGranted($svFiles[0])) {
            foreach ($svFiles as $svFile) {
                $link = $this->router->generate('sinervis_file_uploader_bundle_api_file_download', ['id' => $svFile->getId()]);
                $html .= "<li><a href='$link'>" . $svFile->getOriginalName() . "</a></li>";
            }
        } else {
            foreach ($svFiles as $svFile) {
                $html .= "<li>" . $svFile->getOriginalName() . "</li>";
            }
        }

        return '<ul>'.$html.'</ul>';
    }

    public function formatFileSizeFilter($size)
    {
        return $this->formatBytes($size, 2);
    }

    function formatBytes($size, $precision = 0){
        $unit = ['Byte','kB','MB','GB','TB','PB','EB','ZB','YB'];

        for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
            $size /= 1000;
        }

        return round($size, $precision).' '.$unit[$i];
    }

    public function sinervisImagePreviewLinkFunc(?SvFile $svFile)
    {
        if ($svFile) {
            $image = $svFile->getUri().'/'.$svFile->getName();
            $imageData = base64_encode(file_get_contents($image));
            $src = 'data: ' . mime_content_type($image) . ';base64,' . $imageData;
            return '<img src="' . $src . '">';
        }

        return null;
    }
}