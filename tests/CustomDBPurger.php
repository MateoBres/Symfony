<?php declare(strict_types=1);

namespace App\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrineOrmPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface as DoctrinePurgerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Fidry\AliceDataFixtures\Persistence\PurgerFactoryInterface;
use Fidry\AliceDataFixtures\Persistence\PurgerInterface;
use Nelmio\Alice\IsAServiceTrait;

/**
 * @final
 */
/* final */ class CustomDBPurger implements PurgerInterface, PurgerFactoryInterface
{
    use IsAServiceTrait;

    private $manager;
    private $purger;
    private $purgeMode;
    private $excluded_tables;

    public function __construct(ObjectManager $manager, PurgeMode $purgeMode = null, array $excluded_tables)
    {
        $this->manager = $manager;
        $this->purger = $this->createPurger($manager, $purgeMode, $excluded_tables);
        $this->purgeMode = $purgeMode;
        $this->excluded_tables = $excluded_tables;
    }

    private function createPurger(ObjectManager $manager, ?PurgeMode $purgeMode, $excluded_tables): DoctrinePurgerInterface
    {
        $metaData = $manager->getMetadataFactory()->getAllMetadata();

        $purger = new DoctrineOrmPurger($manager, $excluded_tables);

        if (null !== $purgeMode) {
            $purger->setPurgeMode($purgeMode->getValue());
        }

        return $purger;
    }

    /**
     * @inheritdoc
     */
    public function create(PurgeMode $mode, PurgerInterface $purger = null): PurgerInterface
    {
        if ($this->purger instanceof DoctrinePurgerInterface) {
            $manager = $this->purger->getObjectManager();
        } elseif ($this->purger instanceof self) {
            $manager = $this->purger->manager;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected purger to be either and instance of "%s" or "%s". Got "%s".',
                    DoctrinePurgerInterface::class,
                    __CLASS__,
                    get_class($this->purger)
                )
            );
        }

        if (null === $manager) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected purger "%s" to have an object manager, got "null" instead.',
                    get_class($this->purger)
                )
            );
        }

        return new self($manager, $mode, $this->excluded_tables);
    }

    /**
     * @inheritdoc
     */
    public function purge()
    {
        // Because MySQL rocks, you got to disable foreign key checks when doing a TRUNCATE/DELETE unlike in for example
        // PostgreSQL. This ideally should be done in the Purger of doctrine/data-fixtures but meanwhile we are doing
        // it here.
        // See the progress in https://github.com/doctrine/data-fixtures/pull/272
        $disableFkChecks = (
            $this->purger instanceof DoctrineOrmPurger
            && in_array($this->purgeMode->getValue(), [PurgeMode::createDeleteMode()->getValue(), PurgeMode::createTruncateMode()->getValue()])
            && $this->purger->getObjectManager()->getConnection()->getDriver() instanceof AbstractMySQLDriver
        );

        if ($disableFkChecks) {
            $connection = $this->purger->getObjectManager()->getConnection();

            $connection->exec('SET FOREIGN_KEY_CHECKS = 0;');
        }

        $this->purger->purge();

        if ($disableFkChecks && isset($connection)) {
            $connection->exec('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
}