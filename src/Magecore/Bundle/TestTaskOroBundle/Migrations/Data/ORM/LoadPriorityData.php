<?php

namespace Magecore\Bundle\TestTaskOroBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Magecore\Bundle\TestTaskOroBundle\Entity\Priority;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;

class LoadPriorityData extends AbstractTranslatableEntityFixture implements FixtureInterface
{
    const CASE_PRIORITY_PREFIX = 'case_priority';

    /**
     * @var array
     */
    protected $priorityNames = array(
        1 => Priority::PRIORITY_LOW,
        2 => Priority::PRIORITY_NORMAL,
        3 => Priority::PRIORITY_HIGH,
    );

    /**
     * Load entities to DB
     *
     * @param ObjectManager $manager
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $priorityRepository = $manager->getRepository('MagecoreTestTaskOroBundle:Priority');
        foreach ($this->priorityNames as $order => $priorityName) {
            /** @var Priority $casePriority */
            $casePriority = $priorityRepository->findOneBy(array('name' => $priorityName));
            if (!$casePriority) {
                $casePriority = new Priority($priorityName);
                $casePriority->setOrder($order);
                $casePriority->setLabel($priorityName);
            }
            // save
            $manager->persist($casePriority);
        }
        $manager->flush();
    }
    public function load(ObjectManager $manager)
    {
        $this->loadEntities($manager);
    }
}
