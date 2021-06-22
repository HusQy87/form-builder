<?php

namespace App\Entity;

use App\Repository\UserVerificationTokenRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=UserVerificationTokenRepository::class)
 */
class UserVerificationToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $expires_at;


    public function __construct()
    {
        $this->token = bin2hex(random_bytes(60));
        $this->expires_at = (new DateTime())->setTimestamp(time() + 3600);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expires_at;
    }


}
