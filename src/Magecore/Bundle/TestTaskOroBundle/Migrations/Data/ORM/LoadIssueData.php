<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 27.07.15
 * Time: 11:14
 */


namespace Magecore\Bundle\TestTaskOroBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;
use Magecore\Bundle\TestTaskOroBundle\Entity\Resolution;
use Magecore\Bundle\TestTaskOroBundle\Entity\Priority;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    const CASE_PRIORITY_PREFIX = 'case_priority';

    public function getDependencies()
    {
        return [
            'Magecore\Bundle\TestTaskOroBundle\Migrations\Data\Demo\ORM\LoadPriorityData',
            'Magecore\Bundle\TestTaskOroBundle\Migrations\Data\Demo\ORM\LoadResolutionData',
            'Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData',
        ];
    }

    const ISSUE_COUNT = 20;

    /**
     * @var array
     */
    protected $issueSummary = array(
        'Create a demo data', 'Write tests', 'Remove garbage', 'Duplicate remove'
    );

    /**
     * @var array
     */
    protected $issueDescription = array(
        'Description of issie', 'Errare Humanum Est(c)', 'Do you do you mast do?', 'Best description is "subj".'
    );

    /**
     * @param ObjectManager $manager
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $priorities = $manager->getRepository('MagecoreTestTaskOroBundle:Priority')->findAll();
        if (empty($priorities)) {
            return;
        }
        $resolutions = $manager->getRepository('MagecoreTestTaskOroBundle:Resolution')->findAll();
        if (empty($resolutions)) {
            return;
        }
        $resolutions[]=null;
        $users = $manager->getRepository('OroUserBundle:User')->findAll();
        if (empty($users)) {
            return;
        }
        $assignee = $users;
        $assignee[]=null;

        $issueRepository = $manager->getRepository('MagecoreTestTaskOroBundle:Issue');
        $story = null;
        for ($i=0; $i< $this::ISSUE_COUNT; $i++) {
            $issue = new Issue();
            $issue->setType($issue->getParentTypes()[array_rand($issue->getParentTypes())]);
            if ($story) {
                $issue->setType($issue::ISSUE_TYPE_SUBTASK);
                $issue->getParentIssue($story);
            }
            $issue->setSummary($this->issueSummary[array_rand($this->issueSummary)]);
            $issue->setDescription($this->issueDescription[array_rand($this->issueDescription)]);
            $issue->setPriority($priorities[array_rand($priorities)]);
            $issue->setReporter($users[array_rand($users)]);
            $issue->setResolution($resolutions[array_rand($resolutions)]);
            $issue->getAssignedTo($assignee[array_rand($assignee)]);

            $manager->persist($issue);
        }
        $manager->flush();
    }
    public function load(ObjectManager $manager)
    {
        $this->loadEntities($manager);
    }
}
