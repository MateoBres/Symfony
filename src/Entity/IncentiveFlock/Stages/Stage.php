<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Repository\IncentiveFlock\StageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StageRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *   "Stage" = "Stage",
 *   "ContactWithCustomer" = "ContactWithCustomer",
 *   "DataCollection" = "DataCollection",
 *   "BuildingPractice" = "BuildingPractice",
 *   "SeismicRoutine" = "SeismicRoutine",
 *   "PhotovoltaicPlanning" = "PhotovoltaicPlanning",
 *   "MetricCalculation" = "MetricCalculation",
 *   "ThermotechnicPlanning" = "ThermotechnicPlanning",
 *   "Implementation" = "Implementation",
 *   "Testing" = "Testing",
 *   "TechnicalAssertion" = "TechnicalAssertion",
 *   "FiscalAssertion" = "FiscalAssertion",
 *   "TaxDeductionPaperwork" = "TaxDeductionPaperwork",
 *   "AssignmentOfClaim" = "AssignmentOfClaim",
 * })
 */
class Stage implements TimestampableInterface
{
    use TimestampableTrait;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }
}
