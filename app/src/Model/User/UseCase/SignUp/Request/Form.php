<?php


namespace App\Model\User\UseCase\SignUp\Request;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('email', Type\EmailType::class,[
                'label' => "Введите email",
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('password', Type\PasswordType::class,[
                'label' => 'Пароль',
                'attr'=>[
                    'class'=>'form-control required'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
