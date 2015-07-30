<?php

namespace Magecore\Bundle\TestTaskOroBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', null)
            ->add('description', null)
        ;
        $builder
            ->add(
                'assignedTo',
                'oro_user_organization_acl_select',
                [
                    'required'      => false,
                ]
            );

        if (isset($options['data'])) {
            if ($options['data']->getId() == 0) {
                if ($options['data']->getParentIssue()) {
                    //do nothing.
                    $arr = null;
                } else {
                    $arr = $options['data']->getParentTypes();
                    $arr = array_combine($arr, $arr);
                }
            }
        } else {
            $builder->add('type', null, array(
                'required' => true,
            ));
        }

        if (!empty($arr)) {
            $builder->add('type', 'choice', array(
                'choices' =>
                    $arr,

                'required' => true,
                'empty_value' => false,
                'empty_data'=>null,
            ));
        }
        $builder
        ->add(
            'priority',
            'entity',
            [
            'class'         => 'MagecoreTestTaskOroBundle:Priority',
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('priority')
                    ->orderBy('priority.order', 'ASC');
            }
            ]
        )
        ->add(
            'resolution',
            'entity',
            [
                'required'     => false,
                'class'         => 'MagecoreTestTaskOroBundle:Resolution',
            ]
        )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Magecore\Bundle\TestTaskOroBundle\Entity\Issue',
            'projects' => array(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'magecore_testtaskoro_issue';
    }
}
