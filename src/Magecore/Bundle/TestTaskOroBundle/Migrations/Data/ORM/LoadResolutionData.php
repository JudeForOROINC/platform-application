<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 27.07.15
 * Time: 11:14
 */


namespace Magecore\Bundle\TestTaskOroBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Magecore\Bundle\TestTaskOroBundle\Entity\Resolution;
use Magecore\Bundle\TestTaskOroBundle\Entity\Priority;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;

class LoadResolutionData extends AbstractTranslatableEntityFixture implements FixtureInterface
{
    const CASE_PRIORITY_PREFIX = 'case_priority';

    /**
     * @var array
     */
    protected $resolutionNames = array(
        'Done', 'Fixed', 'Can not reproduce', 'Duplicate'
    );

    /**
     * Load entities to DB
     *
     * @param ObjectManager $manager
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $resolutionRepository = $manager->getRepository('MagecoreTestTaskOroBundle:Resolution');
        foreach ($this->resolutionNames as $resolutionName) {
            /** @var Resolution $issueResolution */
            $issueResolution = $resolutionRepository->findOneBy(array('value' => $resolutionName));
            if (!$issueResolution) {
                $issueResolution = new Resolution($resolutionName);
                $issueResolution->setValue($resolutionName);
            }
            // save
            $manager->persist($issueResolution);
        }
        $manager->flush();
    }
    public function load(ObjectManager $manager)
    {
        $this->loadEntities($manager);
    }
}
