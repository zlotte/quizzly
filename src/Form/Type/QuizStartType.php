<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Answer;
use App\Entity\Question;

class QuizStartType extends AbstractType
{
    private const ANSWERS_IN_PROGRESS = 'answersInProgress';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answers', ChoiceType::class, [
                'label' => $options['data']->getTitle(),
                'expanded' => true,
                'multiple' => true,
                'choices'  => $this->prepareAnswers($options),
                'mapped' => false,
                'data' => $this->selectedAnswers($options),
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);

        $resolver->setRequired([
            self::ANSWERS_IN_PROGRESS,
        ]);
    }

    private function prepareAnswers(array $options): array
    {
        $answers = [];

        /** @var Answer $answer */
        foreach($options['data']->getAnswers() as $answer) {
            $answers[$answer->getTitle()] = $answer->getId();
        }

        return $answers;
    }

    private function selectedAnswers(array $options): array
    {
        $answersInProgress = $options[self::ANSWERS_IN_PROGRESS];
        $selectedQuestionIds = [];
        $answersSelected = [];

        /** @var Answer $questionAnswer */
        foreach($options['data']->getAnswers() as $questionAnswer) {
            foreach($answersInProgress as $selectedAnswer) {
                foreach($selectedAnswer['answers'] as $selectedAnswerAnswer) {
                    if ($questionAnswer->getId() === (int)$selectedAnswerAnswer) {
                        $selectedQuestionIds[] = $selectedAnswerAnswer;
                    }
                }
            }
        }

        /** @var Answer $answer */
        foreach($options['data']->getAnswers() as $answer) {
            foreach($selectedQuestionIds as $questionId) {
                if ((int)$questionId === $answer->getId()) {
                    $answersSelected[$answer->getTitle()] = $answer->getId();
                }
            }
        }
        
        return $answersSelected;
    }
}