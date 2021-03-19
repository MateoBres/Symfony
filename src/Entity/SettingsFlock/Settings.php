<?php

namespace App\Entity\SettingsFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\ContactFlock\Contact;
use App\Entity\UserFlock\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingsFlock\SettingsRepository")
 */
class Settings
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserFlock\User", inversedBy="settings", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $notificationEmails = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $questionnaireHeading;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $questionnaireThankingMsg;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNotificationEmails(): ?array
    {
        return $this->notificationEmails;
    }

    public function setNotificationEmails(?array $notificationEmails): self
    {
        $this->notificationEmails = $notificationEmails;

        return $this;
    }

    public function getQuestionnaireHeading(): ?string
    {
        return $this->questionnaireHeading;
    }

    public function setQuestionnaireHeading(?string $questionnaireHeading): self
    {
        $this->questionnaireHeading = $questionnaireHeading;

        return $this;
    }

    public function getQuestionnaireThankingMsg(): ?string
    {
        return $this->questionnaireThankingMsg;
    }

    public function setQuestionnaireThankingMsg(?string $questionnaireThankingMsg): self
    {
        $this->questionnaireThankingMsg = $questionnaireThankingMsg;

        return $this;
    }
}
