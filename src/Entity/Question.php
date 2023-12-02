<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\QuestionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Quiz;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist'])]
    #[Assert\NotBlank]
    private Collection $answers;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[Assert\NotNull]
    private ?Quiz $quiz = null;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): void
    {
        if ($this->answers->contains($answer)) {
            return;
        }

        $this->answers->add($answer);
        
        $answer->setQuestion($this);
    }

    public function removeAnswer(Answer $answer): void
    {
        if (!$this->answers->contains($answer)) {
            return;
        }

        $this->answers->removeElement($answer);
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }
}