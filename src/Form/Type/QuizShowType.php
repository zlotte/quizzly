<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\QuizCode;
use App\Form\QuestionType;
use App\Form\QuizCodeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Quiz;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuizShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quizCode', TextType::class, [
                'label' => 'quiz_code',
                'translation_domain' => 'messages',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => '1', 'max'=> 255]),
                ],
                'mapped' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }
}