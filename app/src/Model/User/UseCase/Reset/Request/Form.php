<?php


namespace App\Model\User\UseCase\Reset\Request;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email',Type\EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>Command::class,[
                'label' => "Введите email",
                'attr'=>[
                    'class'=>'form-control'
                ]
            ]
        ]);
    }
}
