<?php 

namespace App\Form;

use App\Entity\EmploiDuTemps;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmploiDuTempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Title',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('date_debut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date',
            ])
            ->add('date_fin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'End Date',
            ])
            ->add('recurrent', CheckboxType::class, [
                'required' => false,
                'label' => 'Is Recurrent?',
            ])
            ->add('lieu', TextType::class, [
                'required' => false,
                'label' => 'Location',
            ])
            ->add('matiere', TextType::class, [
                'required' => false,
                'label' => 'Subject',
            ])
            ->add('updated_at', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Updated At',
                'disabled' => true, // Make this read-only if it's automatically set
            ])
            
            
            ->add('enseignant', TextType::class, [
               'required' => true,
                'label' => 'Teacher',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmploiDuTemps::class,
        ]);
    }
}
