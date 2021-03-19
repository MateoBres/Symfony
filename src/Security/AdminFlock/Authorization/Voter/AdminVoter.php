<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 07/01/15
 * Time: 12.34
 */

namespace App\Security\AdminFlock\Authorization\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AdminVoter implements VoterInterface
{
    const LIST_ALL = 'list_all';
    const LIST_OWNED = 'list_owned';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const CREATE = 'create';
    const PDF = 'pdf';
    const CHANGE_PASSWORD = 'change_password';
    const COMMISSION_LIST = 'view commission list';
    const VIEW_COMM_ACTIVITY_CALENDAR = 'view_comm_activity_calendar';
    const VIEW_MENU_ITEM = 'view_menu_link_to_list';

    protected $supportedAttributes = array(
        self::LIST_ALL,
        self::LIST_OWNED,
        self::VIEW,
        self::EDIT,
        self::DELETE,
        self::CREATE,
        self::PDF,
        self::CHANGE_PASSWORD,
        self::COMMISSION_LIST,
        self::VIEW_COMM_ACTIVITY_CALENDAR,
        self::VIEW_MENU_ITEM,
    );
    protected $supportedClasses = 'any'; // or an array of classes

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        if ($object) {
            // check suported class
            if (!$this->supportsClass(str_replace('Proxies\\__CG__\\', '', get_class($object)))) {
                return VoterInterface::ACCESS_ABSTAIN;
            }
        }


        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            // check permission
            $success = $this->decide($token->getUser(), $object, $attribute);

            if ($success) {
                return VoterInterface::ACCESS_GRANTED;
            }

            $result = VoterInterface::ACCESS_DENIED;
        }

        return $result;
    }

    public function supportsClass($class)
    {
        if ($this->supportedClasses == 'any') {
            return true;
        }

        foreach ($this->supportedClasses as $supportedClass) {
            if ($supportedClass === $class || is_subclass_of($class, $supportedClass)) {
                return true;
            }
        }
        return false;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->supportedAttributes);
    }

    protected function decide($user, $object, $attribute)
    {
        // This is useful to disable the permission applier for inserting
        // Customer and Teacher contents by anonymous users.
        if ($user == 'anon.' && $attribute == 'create') {
            return false;
        }
        // admin has all-area pass
        return $this->userHasAdminRole($user, 'ROLE_SUPER_ADMIN') || $this->userHasAdminRole($user, 'ROLE_ADMIN');
    }

    protected function userHasAdminRole($user, $roleName)
    {
        if (is_object($user)) {
            return in_array($roleName, $user->getRoles());
        }
        return false;
    }

    protected function userHasRole($user, $roleNames)
    {
        if (is_object($user) && $user->getContact() && $user->getContact()->getId()) {
            if (is_string($roleNames)) {
                return $user->getContact()->hasRole($this->getNamespacedClass($roleNames));
            } elseif (is_array($roleNames)) {
                foreach ($roleNames as $roleName) {
                    $hasRole = $user->getContact()->hasRole($this->getNamespacedClass($roleName));
                    if ($hasRole) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function getNamespacedClass($className)
    {
        switch ($className) {
            case 'PrivateSeller':
            case 'CompanySeller':
            case 'Agent':
                return 'Sinervis\ContactBundle\Entity\Sellers\\' . $className;

            case 'PrivateAdmin':
            case 'CompanyAdmin':
                return 'Sinervis\ContactBundle\Entity\Admins\\' . $className;

            case 'Customer':
            case 'Teacher':
            case 'Supplier':
            case 'Seller':
                return 'Sinervis\ContactBundle\Entity\Roles\\' . $className;
        }
    }

    protected function getNumberOfRoles($user)
    {
        if (is_object($user) && $user->getContact() && $user->getContact()->getId()) {
            return $user->getContact()->getRoles()->count();
        }

        return;
    }

    protected function hasUserCreatedTheObject($object, $user)
    {
        if ($object && method_exists($object, 'getCreatedBy')) {
            return is_object($object->getCreatedBy()) && is_object($user) && $object->getCreatedBy()->getId() == $user->getId();
        }

        return;
    }
}