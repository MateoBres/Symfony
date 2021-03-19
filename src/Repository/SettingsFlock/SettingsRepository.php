<?php

namespace App\Repository\SettingsFlock;

use App\DBAL\Types\SettingsKindType;
use App\Entity\SettingsFlock\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @method Settings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Settings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Settings[]    findAll()
 * @method Settings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsRepository extends ServiceEntityRepository
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(ManagerRegistry $registry, ContainerInterface $container)
    {
        parent::__construct($registry, Settings::class);
        $this->container = $container;
    }

    public function getGlobalSettings()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->setParameter('type', SettingsKindType::GLOBALS)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getUserSettings()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $user->getId();

        return $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->andWhere('s.user = :user')
            ->setParameter('type', SettingsKindType::USER)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
