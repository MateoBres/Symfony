<?php

namespace App\Repository\AdminFlock;

interface AdminRepositoryInterface
{
    /**
     * @param string $term
     * @param bool $filterMode
     * @return array
     */
    public function findByTermForAutocompletion(string $term, bool $filterMode = false): array;
}