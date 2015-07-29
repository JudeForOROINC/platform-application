<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 23.07.15
 * Time: 14:10
 */


namespace Magecore\Bundle\TestTaskOroBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;

class IssueApiType extends IssueType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Magecore\Bundle\TestTaskOroBundle\Entity\Issue',
            'projects' => array(),
            'csrf_protection'      => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'magecore_testtaskoro_issue_api';
    }
}
