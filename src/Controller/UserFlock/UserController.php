<?php

namespace App\Controller\UserFlock;

use App\Entity\UserFlock\User;
use App\Form\UserFlock\UserType;
use App\Filter\UserFlock\UserFilterType;
use App\Repository\UserFlock\UserRepository;
use App\Controller\AdminFlock\AdminController;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sinervis\NotificationBundle\Event\NotificationEventUserCreate;
use Sinervis\AdminBundle\Security\Authorization\Voter\AdminVoter;

/**
 * @Route("admin/user", name="admin_user")
 */
class UserController extends AdminController
{
    protected $flock_name   = 'UserFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'User';
    protected $templates_path = 'user_flock/user';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();

        // add here your join
        //$query->leftJoin('{{ entity_class }}.childEntity','b')->addSelect('b');

        return $query;
    }

    protected function getNewEntity()
    {
        return new User();
    }

    protected function getNewEntityType()
    {
        return UserType::class;
    }

    protected function getNewEntityFilterType()
    {
        return UserFilterType::class;
    }

    public function changePasswordAction($id)
    {
        $this->preExecute();

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository($this->bundle_name . ':' . $this->entity_name)->find($id);

        if (!$user) {
            $this->get('session')->getFlashBag()->add('error', 'L\'utente non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        // security check
        if (!$this->isGranted(AdminVoter::CHANGE_PASSWORD, $user)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per cambiare la password a questo utente');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $form = $this->container->get('user.change_password.form');
        $form->remove('current_password');
        $form->add('new', 'repeated', array(
            'label' => ' ',
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'form.new_password'),
            'second_options' => array('label' => 'form.new_password_confirmation'),
            'invalid_message' => 'user.password.mismatch',
        ));
        $formHandler = $this->container->get('user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->get('session')->getFlashBag()->add('success', 'La password di ' . $user . ' è stata cambiata.');

            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        return $this->render($this->bundle_name . ':' . $this->entity_name . ':change_password.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'configuration' => $this->getConfiguration(),
            'blocks' => array(
                'row1' => array(
                    'col1' => array(
                        'size' => 7,
                        'blocks' => array(
                            'entity' => array('title' => 'Utente', 'icon' => 'fa-user', 'fields' => array('new'))
                        ),
                    ),
                ),
            )
        ));
    }

    /**
     * @Route("/switch-user/{id}", name="_switch_user")
     * @IsGranted("ROLE_ALLOWED_TO_SWITCH")
     */
    public function switchUserAction($id)
    {
        $this->preExecute();

        $em = $this->getDoctrine()->getManager();

        $user = $this->getRepository()->find($id);

        if (!$user) {
            $this->get('session')->getFlashBag()->add('error', 'L\'utente non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        return $this->redirect($this->generateUrl('app_homepage', array('_switch_user' => $user->getUsername())));
    }

    /**
     * @Route("/show/{username}", name="_show_by_username")
     */
    public function showByUsername($username)
    {
        $this->preExecute();

        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();
        $user = $em->getRepository($fullEntityNamespace)->getUserByUserName($username);
        if ($user) {
            return $this->redirect($this->generateUrl($this->route_prefix . '_show', array('id' => $user->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', 'Utente non trovato');
        return $this->redirect($this->generateUrl($this->route_prefix));
    }


    public function settingsAction(Request $request, $id, $item)
    {
        $this->preExecute();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->bundle_name . ':' . $this->entity_name)->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $value = $request->request->get('value');

        $settings = $entity->getSettings();
        $settings[$item] = $value;

        $entity->setSettings($settings);
        $em->persist($entity);
        $em->flush();
        exit();
    }

    public function contactListAction(Request $request)
    {
        $query = $this->getDoctrine()->getManager()
            ->getRepository('Sinervis\ContactBundle\Entity\Contact')
            ->createQueryBuilder('c')
            ->leftJoin('c.roles', 'r')
            ->andWhere('
        (r INSTANCE OF \Sinervis\ContactBundle\Entity\Roles\Customer)
        OR (r INSTANCE OF \Sinervis\ContactBundle\Entity\Roles\Supplier)
        OR (r INSTANCE OF \Sinervis\ContactBundle\Entity\Roles\Agent)
        OR (r INSTANCE OF \Sinervis\ContactBundle\Entity\Roles\Seller)
        OR (r INSTANCE OF \Sinervis\ContactBundle\Entity\Roles\Quality)')
            ->setMaxResults(25);

        if ($request->get('term')) {
            $query
                ->where($query->expr()->like('c.fullNameCanonical', ':name'))
                ->setParameter('name', '%' . $request->get('term') . '%');
        }

        $objects = $query->getQuery()->getResult();

        $json = array();
        foreach ($objects as $object) {
            $json[] = array(
                'label' => $object->__toString(),
                'value' => $object->getId(),
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }

    public function userListAction(Request $request)
    {
        $query = $this->getDoctrine()->getManager()
            ->getRepository('Sinervis\UserBundle\Entity\User')
            ->createQueryBuilder('u')
            ->setMaxResults(25);;

        if ($request->get('term')) {
            $query
                ->where($query->expr()->like('u.usernameCanonical', ':name'))
                ->setParameter('name', '%' . $request->get('term') . '%');
        }

        // apply filter to select only active users
        if ($this->getRequest()->get('_route') == 'admin_active_user_ajax_list') {
            $query
                ->andWhere($query->expr()->like('u.enabled', ':enabled'))
                ->setParameter('enabled', 1);
        }

        $objects = $query->getQuery()->getResult();

        $json = array();
        foreach ($objects as $object) {
            $json[] = array(
                'label' => $object->__toString(),
                'value' => $object->getId(),
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }

    public function userCommercialListAction(Request $request)
    {
        $query = $this->getDoctrine()->getManager()
            ->getRepository('Sinervis\UserBundle\Entity\User')
            ->createQueryBuilder('u')
            ->join('u.contact', 'c')
            ->join('c.roles', 'r')
            ->setMaxResults(25);

        if ($request->get('term')) {
            $query->where($query->expr()->like('u.usernameCanonical', ':name'))
                ->setParameter('name', '%' . $request->get('term') . '%');
        }

        $query->andWhere('u.enabled = 1')
            ->andWhere("r INSTANCE OF Sinervis\ContactBundle\Entity\Roles\Seller");

        $objects = $query->getQuery()->getResult();

//        dump($this->getDoctrine()->getManager()
//                ->getRepository('Sinervis\ContactBundle\Entity\Role')
//                ->createQueryBuilder('r')
//                ->where('r INSTANCE OF Sinervis\ContactBundle\Entity\Roles\Seller')
//                ->getQuery()->getResult());
//
//        dump($objects);

        $json = array();
        foreach ($objects as $object) {
            $json[] = array(
                'label' => $object->__toString(),
                'value' => $object->getId(),
            );
        }

        $response = new Response("<html><body>".json_encode($json)."</body></html>");
        //$response->setContent(json_encode($json));

        return $response;
    }

    protected function addSecurityFilter(QueryBuilder $query)
    {
        $query->andWhere($this->getQueryMainAliasName() . '.id = :security_user_id')
            ->setParameter('security_user_id', $this->getUser()->getId());
    }
}
