<?php

namespace App\Controller\TicketFlock;

use App\Entity\TicketFlock\Ticket;
use App\Form\TicketFlock\TicketType;
use App\Filter\TicketFlock\TicketFilterType;
use App\Repository\TicketFlock\TicketRepository;
use App\Controller\AdminFlock\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/ticket_flock_ticket", name="ticket_flock_ticket")
 */
class TicketController extends AdminController
{
    protected $flock_name   = 'TicketFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Ticket';
    protected $templates_path = 'ticket_flock/ticket';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Ticket::class);
    }

    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();
        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
            ->leftJoin('Ticket.interventions', 'intervention')
            ->addSelect('intervention')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');

        return $query;
    }

    protected function getNewEntity()
    {
        return new Ticket();
    }

    protected function getNewEntityType()
    {
        return TicketType::class;
    }

    protected function getNewEntityFilterType()
    {
        return TicketFilterType::class;
    }
}
