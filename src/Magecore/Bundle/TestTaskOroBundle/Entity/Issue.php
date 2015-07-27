<?php

namespace Magecore\Bundle\TestTaskOroBundle\Entity;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
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
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @ORM\Entity()
 * @ORM\Table(name="magecore_testtaskoro_issue")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *   defaultValues={
 *          "security"={
 *              "type"="ACL"
 *          }
 * }
 * )
 * @JMS\ExclusionPolicy("ALL")
 */
class Issue extends ExtendIssue
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
    const ROLE_ANONYMOUS = 'IS_AUTHENTICATED_ANONYMOUSLY';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=false)
     * @JMS\Type("string")
     * @JMS\Expose
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     * )
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=14, unique=true)
     * @JMS\Type("string")
     * @JMS\Expose
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     * )
     */
    protected $code = 'none';


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "merge"={
     *              "display"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=30,
     *              "short"=true
     *          }
     *      }
     * )
     */
    private $reporter;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assigned_to_id", referencedColumnName="id", onDelete="SET NULL")
     * @ConfigField(
     *      defaultValues={
     *          "merge"={
     *              "display"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=30,
     *              "short"=true
     *          }
     *      }
     * )
     * @Oro\Versioned
     */
    protected $assignedTo;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $description;


    //const ISSUE_PARENT_TYPES = [ self::ISSUE_TYPE_STORY, self::ISSUE_TYPE_BUG, self::ISSUE_TYPE_TASK];


    /**
     * @ORM\Column(name="issue_type", type="string", length=30)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Priority")
     * @ORM\JoinColumn(name="priority_name", referencedColumnName="name", onDelete="SET NULL")
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $priority;

//    /**
//     * @ORM\ManyToOne(targetEntity="DicStatus")
//     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
//     */
//    private $status;

//    /**
//     * @ORM\ManyToOne(targetEntity="DicResolution")
//     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
//     */
//    private $resolution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *

     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

//    /**
//     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parentIssue")
//     */
//    protected $children;


//    /**
//     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children", cascade={"persist", "remove"})
//     * @ORM\JoinColumn(name="parent_issue_id", referencedColumnName="id", onDelete="CASCADE")
//     */
//    private $parentIssue;


    const ISSUE_TYPE_STORY = 'Story';
    const ISSUE_TYPE_BUG = 'Bug';
    const ISSUE_TYPE_TASK = 'Task';
    const ISSUE_TYPE_SUBTASK = 'Subtask';



//    /**
//     * @ORM\ManyToMany(targetEntity="User", inversedBy="issues")
//     * @ORM\JoinTable(name="magecore_testtask_issue_to_users"),
//     */
//    protected $collaborators;

//    /**
//     * @ORM\OneToMany(targetEntity="Activity", mappedBy="issue")
//     */
//    protected $activities;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * Set code
     *
     * @param string $code
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Issue
     */
    protected function isValidType($type){
        return in_array( $type ,[self::ISSUE_TYPE_STORY,self::ISSUE_TYPE_BUG, self::ISSUE_TYPE_SUBTASK, self::ISSUE_TYPE_TASK]);
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        if ($this->isValidType($type)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isStory()
    {
        return (bool)($this->getType()==self::ISSUE_TYPE_STORY);
    }

    /**
     * @return bool
     */
    public function isSubtask()
    {
        return (bool)($this->getType()==self::ISSUE_TYPE_SUBTASK);
    }

    /**
     * @return array
     */
    public function getParentTypes()
    {
        return array(
            self::ISSUE_TYPE_BUG,
            self::ISSUE_TYPE_TASK,
            self::ISSUE_TYPE_STORY,
        );
    }


    /**
     * Set priority
     *
     * @param integer $priority
     * @return Issue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Issue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
      //  $this->children = new \Doctrine\Common\Collections\ArrayCollection();
       // $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
       // $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
       // $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->created = new
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @param User $assignee
     * @return CaseEntity
     */
    public function setAssignedTo($assignee)
    {
        $this->assignedTo = $assignee;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set reporter
     *
     * @param User $reporter
     * @return Issue
     */
    public function setReporter(User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

//    /**
//     * Set resolution
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\DicResolution $resolution
//     * @return Issue
//     */
//    public function setResolution(\Magecore\Bundle\TestTaskBundle\Entity\DicResolution $resolution = null)
//    {
//        $this->resolution = $resolution;
//
//        return $this;
//    }
//
//    /**
//     * Get resolution
//     *
//     * @return \Magecore\Bundle\TestTaskBundle\Entity\DicResolution
//     */
//    public function getResolution()
//    {
//        return $this->resolution;
//    }

//    /**
//     * Add children
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\Issue $children
//     * @return Issue
//     */
//    public function addChild(\Magecore\Bundle\TestTaskBundle\Entity\Issue $children)
//    {
//        $this->children[] = $children;
//
//        return $this;
//    }
//
//    /**
//     * Remove children
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\Issue $children
//     */
//    public function removeChild(\Magecore\Bundle\TestTaskBundle\Entity\Issue $children)
//    {
//        $this->children->removeElement($children);
//    }

//    /**
//     * Get children
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getChildren()
//    {
//        return $this->children;
//    }

//    /**
//     * Set parentIssue
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\Issue $parentIssue
//     * @return Issue
//     */
//    public function setParentIssue(\Magecore\Bundle\TestTaskBundle\Entity\Issue $parentIssue = null)
//    {
//        $this->parentIssue = $parentIssue;
//
//        return $this;
//    }
//
//    /**
//     * Get parentIssue
//     *
//     * @return \Magecore\Bundle\TestTaskBundle\Entity\Issue
//     */
//    public function getParentIssue()
//    {
//        return $this->parentIssue;
//    }

    public function __toString()
    {
        return (string)$this->getCode();
    }


//
//    /**
//     * Add collaborators
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\User $collaborators
//     * @return Issue
//     */
//    public function addCollaborator(\Magecore\Bundle\TestTaskBundle\Entity\User $collaborator)
//    {
//        if (!$this->getCollaborators()->contains($collaborator)) {
//            $this->collaborators[] = $collaborator;
//            $collaborator->addIssue($this);
//        }
//
//
//        return $this;
//    }
//
//    /**
//     * Remove collaborators
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\User $collaborators
//     */
//    public function removeCollaborator(\Magecore\Bundle\TestTaskBundle\Entity\User $collaborators)
//    {
//        $this->collaborators->removeElement($collaborators);
//    }
//
//    /**
//     * Get collaborators
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getCollaborators()
//    {
//        return $this->collaborators;
//    }

//    /**
//     * Add activities
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\Activity $activities
//     * @return Issue
//     */
//    public function addActivity(\Magecore\Bundle\TestTaskBundle\Entity\Activity $activities)
//    {
//        $this->activities[] = $activities;
//
//        return $this;
//    }
//
//    /**
//     * Remove activities
//     *
//     * @param \Magecore\Bundle\TestTaskBundle\Entity\Activity $activities
//     */
//    public function removeActivity(\Magecore\Bundle\TestTaskBundle\Entity\Activity $activities)
//    {
//        $this->activities->removeElement($activities);
//    }
//
//    /**
//     * Get activities
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getActivities()
//    {
//        return $this->activities;
//    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = $this->createdAt ? : new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = clone $this->createdAt;
    }

    /**
     * @ORM\PostPersist
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->code = "ISS-".$this->getId();
        /** @var EntityManager $man */
        $man = $args->getObjectManager();
        $unit = $man->getUnitOfWork();
        $unit->scheduleExtraUpdate($this,array('code'=>array('none',$this->code)));

    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }
}
