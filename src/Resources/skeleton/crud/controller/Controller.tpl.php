<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
use <?= $form_filter_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use App\Controller\AdminFlock\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/<?= $route_name ?>", name="<?= $route_name ?>")
 */
class <?= $class_name ?> extends AdminController
{
    protected $flock_name   = '<?= $flock_name ?>';
    protected $entity_namespace = '<?= $entity_namespace ?>';
    protected $entity_name   = '<?= $entity_class_name ?>';
    protected $templates_path = '<?= $templates_path ?>';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(<?= $entity_class_name ?>::class);
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
        return new <?= $entity_class_name ?>();
    }

    protected function getNewEntityType()
    {
        return <?= $form_class_name ?>::class;
    }

    protected function getNewEntityFilterType()
    {
        return <?= $entity_class_name ?>FilterType::class;
    }
}
