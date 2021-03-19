<?php

namespace App\EventSubscribers\ContactFlock;

use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\Event\AfterEvent;
use Knp\Component\Pager\Event\BeforeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RoleNameSortSubscriber implements EventSubscriberInterface
{
    /** @var Request */
    private $request;
    private $sortByRoleName = false;
    private $sortPropertyPath = '';

    CONST ROLE_NAME_FIELD_NAME = 'roleName';

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'knp_pager.after' => ['after', 1/*increased priority to override any internal*/],
            'knp_pager.before' => ['before', 1/*increased priority to override any internal*/]
        ];
    }

    public function before(BeforeEvent $event)
    {
        if ($this->request->query->has('sort')) {
            $sortPropertyPathItems = explode('.', $this->request->query->get('sort'));
            if (is_array($sortPropertyPathItems) && end($sortPropertyPathItems) == self::ROLE_NAME_FIELD_NAME) {
                $this->sortByRoleName = true;
                $this->sortPropertyPath = $this->request->query->get('sort');
                $this->request->query->remove('sort');
            }
        }
    }

    public function after(AfterEvent $event)
    {
        if($this->sortByRoleName) {
            $direction = $this->request->query->get('direction', 'asc');
            $paginationCollection = new ArrayCollection($event->getPaginationView()->getItems());

            $pathItems = explode('.', $this->sortPropertyPath);

            $iterator = $paginationCollection->getIterator();
            $iterator->uasort(function($a, $b) use($pathItems, $direction){
                if(count($pathItems) > 1) {
                    $roleNameA = $this->getRoleName($a);
                    $roleNameB = $this->getRoleName($b);
                } else {
                    $getter = 'get'.ucfirst(self::ROLE_NAME_FIELD_NAME);
                    $roleNameA = $a->$getter();
                    $roleNameB = $b->$getter();
                }

                return $this->compare($roleNameA, $roleNameB, $direction);
            });

            $event->getPaginationView()->setItems($iterator->getArrayCopy());
            $event->stopPropagation();
        }
    }

    private function compare($str1, $srt2, $direction)
    {
        if($direction == 'asc'){
            return strcmp($str1, $srt2);
        } elseif ($direction == 'desc'){
            return strcmp($srt2, $str1);
        } else {
            return 0;
        }
    }

    private function getRoleName($object)
    {
        $pathItems = explode('.', $this->sortPropertyPath);

        if (!is_object($object)) {
            return $object;
        }

        foreach ($pathItems as $pathItem) {
            $getter = 'get' . ucfirst($pathItem);
            if (method_exists($object, $getter)) {
                return $this->getRoleName($object->$getter());
            }
        }
    }
}