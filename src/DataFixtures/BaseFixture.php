<?php

namespace App\DataFixtures;


use App\Entity\CourseFlock\CourseEdition;
use App\Service\SettingsFlock\SettingsManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    protected $manager;

    /** @var Generator */
    protected $faker;

    /** @var UserManager */
    protected $userManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    private $referencesIndex = [];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SettingsManager $settingsManager)
    {
//        $this->userManager = $userManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create('it_IT');
        $this->faker->seed(rand(0, 9999));
        $this->settingsManager = $settingsManager;
    }

    abstract protected function execute();

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->execute();
        $this->manager->flush();
    }

    /**
     * Create many objects at once:
     *
     *      $this->createMany(10, function() {
     *          $user = new User();
     *          $user->setFirstName('Ryan');     *
     *          return $user;
     *      });
     *
     * @param int $count
     * @param callable $factory
     */
    protected function createMany(int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $factory();

            if (null === $entity) {
                throw new \LogicException('Did you forget to return the entity object from your callback to BaseFixture::createMany()?');
            }

            $this->addCreationInfo($entity);
            $this->manager->persist($entity);

            $class = (new ReflectionClass($entity))->getShortName();

            $this->addReference(sprintf('%s_%d', $class, $i), $entity);
        }
    }

    protected function normalizeClassName(&$className)
    {
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

    protected function addReferenceFromEntity($entity)
    {
        $className = '';
        try {
            $className = (new ReflectionClass($entity))->getShortName();
        } catch (\ReflectionException $e) {
            dump($entity);
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return;
        }


        $references = $this->getReferencesAsArray($className);
        if ((new ArrayCollection($references))->contains($entity)) {
            return;
        }
        $index = $references ? max(array_keys($references)) + 1 : 0;

        $this->addReference(sprintf('%s_%d', $className, $index), $entity);
    }

    /**
     * @param $className
     * @return array
     */
    protected function getReferencesAsArray(string $className)
    {
        $this->normalizeClassName($className);

        $references = [];
        foreach ($this->referenceRepository->getReferences() as $key => $ref) {
            if (strpos($key, $className . '_') === 0) {
                $parts = explode('_', $key);
                $index = @$parts[1] ?? 0;
                $references[$index] = $ref;
            }
        }

        return $references;
    }

    protected function getReferencesAsArrayCollection(string $className)
    {
        $this->normalizeClassName($className);

        $references = [];
        foreach ($this->referenceRepository->getReferences() as $key => $ref) {
            if (strpos($key, $className . '_') === 0) {
                $references[] = $ref;
            }
        }

        return new ArrayCollection($references);
    }

    /**
     * @param string $className
     * @return object
     */
    protected function getRandomReference(string $className)
    {
        $this->normalizeClassName($className);

        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $className . '_') !== false) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className])) {
            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the group name "%s"', $className));
        }

        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);

//        $references = $this->getReferencesAsArray($className);
//        if (empty($references)) {
//            throw new \InvalidArgumentException(sprintf('Did not find any references saved with the group name "%s"', $className));
//        }
//        $randomReferenceKey = $this->faker->randomElement($references);

        return $this->getReference($randomReferenceKey);
    }

    /**
     * @param string $className
     * @param int $count
     * @return array
     */
    protected function getRandomReferences(string $className, int $count)
    {
        $this->normalizeClassName($className);

        $references = [];
        while (count($references) < $count) {
            $references[] = $this->getRandomReference($className);
        }

        return $references;
    }

    protected function addCreationInfo($object)
    {
        if (method_exists($object, 'setCreatedBy') && method_exists($object, 'setUpdatedBy')) {
            $object->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
            $object->setUpdatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        }
    }

    protected function registerAndPersist($entity, $addReference = true)
    {
        $this->addCreationInfo($entity);
        if ($addReference) $this->addReferenceFromEntity($entity);
        $this->manager->persist($entity);
    }
}
