<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuizCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\AnswerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuizCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', TextType::class, [
            'label' => 'quiz_code',
            'translation_domain' => 'messages',
            'constraints' => [
                new NotBlank(),
                new Length(['min'=> 5, 'max'=> 255]),
            ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizCode::class,
        ]);
    }
}