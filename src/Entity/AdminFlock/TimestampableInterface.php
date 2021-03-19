<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 05/12/14
 * Time: 10.30
 */

namespace App\Entity\AdminFlock;


interface TimestampableInterface
{
    public function setCreatedBy($createdBy);

    public function getCreatedBy();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedBy($updatedBy);

    public function getUpdatedBy();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();

}