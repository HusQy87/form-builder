<?php

namespace App\Entity;

use App\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormRepository::class)
 */
class Form
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Section::class, mappedBy="form", orphanRemoval=true)
     */
    private $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addQuestion(Section $question): self
    {
        if (!$this->sections->contains($question)) {
            $this->sections[] = $question;
            $question->setForm($this);
        }

        return $this;
    }

    public function removeQuestion(Section $question): self
    {
        if ($this->sections->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getForm() === $this) {
                $question->setForm(null);
            }
        }

        return $this;
    }
}
