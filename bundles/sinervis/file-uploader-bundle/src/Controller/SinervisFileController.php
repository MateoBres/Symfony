<?php


namespace Sinervis\FileUploaderBundle\Controller;

use App\Security\AdminFlock\AdminVoter;
use Doctrine\Persistence\ManagerRegistry;
use Sinervis\FileUploaderBundle\Entity\SvFile;
use Sinervis\FileUploaderBundle\Service\SinervisDownloadHelper;
use Sinervis\FileUploaderBundle\Service\SinervisUploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class SinervisFileController
{
    private $uploaderHelper;
    private $downloadHelper;
    private $doctrine;

    public function __construct(SinervisUploaderHelper $uploaderHelper, SinervisDownloadHelper $downloadHelper, ManagerRegistry $doctrine)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->downloadHelper = $downloadHelper;
        $this->doctrine = $doctrine;
    }

    /**
     * @return JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $dataClass = $request->request->get('data_class');
        $propertyName = $request->request->get('property_name');
        $uploadedFile = $request->files->get('sv_file');

        return $this->uploaderHelper->handleFileUpload($uploadedFile, $dataClass, $propertyName);
    }

    public function downloadFile(Request $request, $id)
    {
        /** @var SvFile $svFile */
        $svFile = $this->doctrine->getRepository(SvFile::class)->find($id);

        if (!$svFile) {
//            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            throw new NotFoundHttpException("'L\'elemento non è stato trovato!");
        }

        if (!$this->downloadHelper->isPermissionGranted($svFile)) {
//            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per visualizzare questo file.');
            throw $this->createAccessDeniedException('Non hai i permessi per visualizzare questo file.');
        }

        $response = new BinaryFileResponse($svFile->getUri().'/'.$svFile->getName());
        $response->headers->set('Content-Type', $svFile->getMimeType());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $svFile->getOriginalName());

        return $response;
    }
}