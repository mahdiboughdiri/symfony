<?php

// src/Form/BookType.php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('publicationDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Publication Date',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'username',
                'label' => 'Author',
            ]);;
        if ($options['is_edit']) {
            $builder->add('enabled', CheckboxType::class, [
                'label' => 'Published',
                'required' => false,
            ]);
        }
        $builder->add('category', ChoiceType::class, [
            'choices' => [
                'Science-Fiction' => 'Science-Fiction',
                'Mystery' => 'Mystery',
                'Autobiography' => 'Autobiography',
            ],
            'label' => 'Category'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'is_edit' => false,
        ]);
    }
}
