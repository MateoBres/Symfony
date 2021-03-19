<?php

namespace App\Controller\ContactFlock\Roles;

use App\Annotations\ContactRoleMap;
use App\Controller\AdminFlock\AdminController;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

abstract class RoleController extends AdminController
{
    /**
     * @Route("/{id}/edit", name="_edit")
     * @param Request $request
     * @param $id
     * @param bool $redirect_to_show
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function edit(Request $request, $id, $redirect_to_show = true)
    {
        $entity = $this->getRepository()->find($id);

        $baseRoute = $this->route_helper->getRouteName($entity->getContact());
        $reflector = new \ReflectionClass($entity);

        return $this->redirectToRoute($baseRoute.'_edit', [
            'id' => $entity->getContact()->getId(),
            'role' => $reflector->getShortName(),
            '_fragment' => $reflector->getShortName(),
        ]);
    }

    /**
     * @Route("/new", name="_new", options={"expose"=true})
     * @param Request $request
     * @param bool $redirect_to_show
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function new(Request $request, $redirect_to_show = true)
    {
        $this->preExecute();

        $entity = $this->getNewEntity();
        $reflector = new \ReflectionClass($entity);
        $contact = $this->getContactEntity($reflector->getShortName());

        $baseRoute = $this->route_helper->getRouteName($contact);

        return $this->redirectToRoute($baseRoute.'_new', [
            'role' => $reflector->getShortName(),
        ]);
    }

    /**
     * @param $roleName
     * @return Company|Person
     * @throws ReflectionException
     */
    private function getContactEntity($roleName)
    {
        $contact = new Person();

        $allowedRoleMap = $this->annotationReader->getClassAnnotation(new \ReflectionClass($contact), ContactRoleMap::class);
        $allowedRoles = $allowedRoleMap->value;

        if (array_key_exists($roleName, $allowedRoles)) {
            return $contact;
        }

        return new Company();
    }

    /**
     * @Route("/{id}/show", name="_show")
     * @param $id
     * @return RedirectResponse
     * @throws ReflectionException
     */
    public function show($id)
    {
        $entity = $this->getRepository()->find($id);
        $reflector = new \ReflectionClass($entity);
        $baseRoute = $this->route_helper->getRouteName($entity->getContact());

        return $this->redirectToRoute($baseRoute.'_show', [
            'id' => $entity->getContact()->getId(),
            'role' => $reflector->getShortName(),
        ]);
    }
}