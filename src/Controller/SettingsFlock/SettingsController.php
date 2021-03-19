<?php

namespace App\Controller\SettingsFlock;

use App\Controller\AdminFlock\AdminController;
use App\DBAL\Types\SettingsKindType;
use App\Entity\SettingsFlock\Settings;
use App\Filter\SettingsFlock\SettingsFilterType;
use App\Form\SettingsFlock\SettingsType;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/settings", name="settings_flock_settings")
 */
class SettingsController extends AdminController
{
    protected $flock_name   = 'SettingsFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Settings';
    protected $templates_path = 'settings_flock';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Settings::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();

        return $query;
    }

    protected function getNewEntity()
    {
        return new Settings();
    }

    protected function getNewEntityType()
    {
        return SettingsType::class;
    }

    protected function getNewEntityFilterType()
    {
        return SettingsFilterType::class;
    }

    /**
     * @Route("/edit", name="_edit_globals")
     * @param Request $request
     * @param bool $redirect_to_show
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editGlobals(Request $request, $redirect_to_show = true)
    {
        $entity = $this->getRepository()->findOneBy(['type' => SettingsKindType::GLOBALS]);
        $entity->setUpdatedAt(new \DateTime());

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        // security check
        if (false === $this->isGranted(AdminVoter::EDIT, $entity)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per modificare questo ' . $this->singular_name);
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $this->setDefaultsForEntity($entity, 'edit', $request);

        $editForm = $this->createFormWithCustomOptions($entity, $request);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $entity = $this->getRepository()->find($entity->getId());

            $this->get('session')->getFlashBag()->add('success', 'L\'elemento è stato salvato!');

//            $response = $this->onPostUpdate($request, $entity, $redirect_to_show);
//            if($response)
//                return $response;
//
//            $link = $this->generateUrl($this->route_prefix);
//            return $this->redirect($link);
//

            return $this->redirect($this->generateUrl($this->route_prefix . '_show',
                array_merge(array('id' => $entity->getId()), $this->route_params)));
        }

        $this->preRender($entity, 'edit');

        if ($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato salvato, correggi gli errori');
        }

        $renderVars = [
            'entity' => $entity,
            'entity_form' => $editForm->createView(),
            'form_errors' => $this->form_errors->getArray($editForm),
            'configuration' => $this->getConfiguration(),
            'blocks' => $this->edit_blocks,
        ];

        $this->tweakRenderVariables($renderVars, 'edit');

        return $this->render($this->templates_path . '/edit.html.twig', $renderVars);
    }

    /**
     * @Route("/show", name="_show_globals")
     * @return RedirectResponse|Response
     */
    public function showGlobals()
    {
        $entity = $this->getRepository()->findOneBy(['type' => SettingsKindType::GLOBALS]);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        // security check
        if (false === $this->isGranted(AdminVoter::VIEW, $entity)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per visualizzare questo ' . $this->singular_name);
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $this->preRender($entity, 'show');

        $renderVars = [
            'entity' => $entity,
            'configuration' => $this->getConfiguration(),
            'blocks' => $this->show_blocks,
            'new_dummy_entity' => $this->getNewEntity(),
        ];

        $this->tweakRenderVariables($renderVars, 'show');

        return $this->render($this->templates_path . '/show.html.twig', $renderVars);
    }
}
