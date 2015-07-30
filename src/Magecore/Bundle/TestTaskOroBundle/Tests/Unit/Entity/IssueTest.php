<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Unit\Entity;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;
use Magecore\Bundle\TestTaskOroBundle\Model\ExtendIssue;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use JMS\Serializer\Annotation as JMS;

use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;

use Oro\Bundle\EmailBundle\Entity\EmailOwnerInterface;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EmailBundle\Entity\EmailOrigin;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

use Oro\Bundle\TagBundle\Entity\Tag;
use Oro\Bundle\TagBundle\Entity\Taggable;

use Oro\Bundle\ImapBundle\Entity\ImapEmailOrigin;
use Oro\Bundle\LocaleBundle\Model\FullNameInterface;
use Oro\Bundle\NotificationBundle\Entity\NotificationEmailInterface;

use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;

use Oro\Bundle\UserBundle\Security\AdvancedApiUserInterface;
use Oro\Bundle\UserBundle\Model\ExtendUser;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Issue
     */
    protected $issue;

    protected function setUp()
    {
        $this->issue = new Issue();
    }

    protected function tearDown()
    {
        unset($this->issue);
    }

    protected function setId($value)
    {
        $r = new \ReflectionClass($this->issue);
        $prop = $r->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($this->issue, $value);
        $prop->setAccessible(false);
    }

    public function testId()
    {
        $this->assertNull($this->issue->getId());
        $value = 1;
        $this->setId($value);
        $this->assertEquals($value, $this->issue->getId());
    }

    /**
     * @param $property
     * @param $value
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = $this->issue;

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $testIssuePriority = $this->getMockBuilder('Magecore\Bundle\TestTaskOroBundle\Entity\Priority')
            ->disableOriginalConstructor()
            ->getMock();
        $testIssuePriority->expects($this->once())->method('getName')->will($this->returnValue('low'));
        $testIssuePriority->expects($this->once())->method('getLabel')->will($this->returnValue('Low label'));

        $testIssueResolution = $this->getMockBuilder('Magecore\Bundle\TestTaskOroBundle\Entity\Resolution')
            ->disableOriginalConstructor()
            ->getMock();
        $testIssueResolution->expects($this->once())->method('getName')->will($this->returnValue('Fixed'));
        //$testIssueResolution->expects($this->once())->method('getLabel')->will($this->returnValue('Low label'));

        $testAssignee = $this->getMockBuilder('OroCRM\Bundle\TaskBundle\Entity\Priority')
            ->disableOriginalConstructor()
            ->getMock();
        $testAssignee->expects($this->once())->method('getName')->will($this->returnValue('low'));
        $testAssignee->expects($this->once())->method('getLabel')->will($this->returnValue('Low label'));
        $organization = $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization');
        return array(
            array('summary', 'Test subject'),
            array('code', 'ISS-2'),
            array('description', 'Test Description'),
            array('type', 'Bug'),
            array('priority', $testIssuePriority),
            array('resolution', $testIssueResolution),
            array('assignedTo', $this->getMock('Oro\Bundle\UserBundle\Entity\User')),
            array('reporter', $this->getMock('Oro\Bundle\UserBundle\Entity\User')),
            array('createdAt', new \DateTime()),
            array('updatedAt', new \DateTime()),
            array('parentIssue', $this->getMock('Magecore\Bundle\TestTaskOroBundle\Entity\Issue')),
            array('organization', $organization),
        );
    }
    public function testPrePersist()
    {
        $obj = $this->issue;
        $this->assertNull($obj->getCreatedAt());
        $this->assertNull($obj->getUpdatedAt());
        $obj->prePersist();
        $this->assertInstanceOf('\DateTime', $obj->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $obj->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        $obj = $this->issue;
        $this->assertNull($obj->getUpdatedAt());
        $obj->preUpdate();
        $this->assertInstanceOf('\DateTime', $obj->getUpdatedAt());
    }

    public function testPostPersist()
    {
        $value = 5;
        $this->setId($value);
        $arr = array('code'=>array('none','ISS-'.$value));
        $LifecycleEventArgs = $this->getMockBuilder('Doctrine\Common\Persistence\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()->getMock();
        $man = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->disableOriginalConstructor()->getMock();
        $unit = $this->getMockBuilder('\Doctrine\ORM\UnitOfWork')->disableOriginalConstructor()->getMock();

        $LifecycleEventArgs->expects($this->once())->method('getObjectManager')->will($this->returnValue($man));
        $man->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($unit));
        $unit->expects($this->once())->method('scheduleExtraUpdate')->with(
            $this->isInstanceOf('Magecore\Bundle\TestTaskOroBundle\Entity\Issue'),
            $this->equalTo($arr)
        );
        $obj = $this->issue;
        $this->assertEquals('none', $obj->getCode());
        $obj->postPersist($LifecycleEventArgs);
        $this->assertEquals('ISS-5', $obj->getCode());
    }

    public function testToString()
    {
        $entity = $this->issue;
        $entity->setCode('ISS-12');
        $this->assertEquals($entity->getCode(), (string)$entity);
    }

    public function testIsStory()
    {
        $entity = $this->issue;
        $entity->setType('Story');
        $this->assertTrue($entity->isStory());
        $entity->setType('Bug');
        $this->assertFalse($entity->isStory());
    }

    public function testIsSubtask()
    {
        $entity = $this->issue;
        $entity->setType($entity::ISSUE_TYPE_SUBTASK);
        $this->assertTrue($entity->isSubtask());
        $entity->setType('Bug');
        $this->assertFalse($entity->isSubtask());
    }

    public function testParentTypes()
    {
        $entity = $this->issue;

        $this->assertEquals([
            $entity::ISSUE_TYPE_BUG,
            $entity::ISSUE_TYPE_TASK,
            $entity::ISSUE_TYPE_STORY,
        ], $entity->getParentTypes());
    }
    public function testOwner()
    {
        $entity = $this->issue;
        $reporter = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $entity->setReporter($reporter);
        $this->assertEquals($reporter, $entity->getOwner());
    }
    public function testAddchild()
    {
        $entity = $this->issue;
        $child = new Issue();
        $this->setId(5);

        $this->assertEmpty($entity->getChildren()->toArray());

        $entity->addChild($child);
        $actualList = $entity->getChildren()->toArray();
        $this->assertCount(1, $actualList);
        $this->assertEquals($child, current($actualList));
    }

    public function testRemoveContact()
    {
        $entity = $this->issue;
        $child = new Issue();
        $this->setId(5);

        $entity->addChild($child);
        $this->assertCount(1, $entity->getChildren()->toArray());

        $entity->removeChild($child);
        $this->assertEmpty($entity->getChildren()->toArray());
    }
}
