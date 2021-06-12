<?php
declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Create;


use App\ReadModel\Work\Members\Group\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private GroupFetcher $group;

    public function __construct(GroupFetcher $group){
        $this->group = $group;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group',Type\ChoiceType::class,['choices'=>array_flip($this->group->assoc())])
            ->add('firstName',Type\TextType::class)
            ->add('LastName',Type\TextType::class)
            ->add('email',Type\EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class'=>Command::class
        ]);
    }

}