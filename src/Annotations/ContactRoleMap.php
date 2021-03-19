<?php


namespace App\Annotations;


use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ContactRoleMap implements Annotation
{
    /**
     * @var array<string>
     */
    public $value;
}