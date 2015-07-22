<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 22.07.15
 * Time: 12:00
 */
// src/InventoryBundle/Controller/VehicleController.php
namespace Magecore\Bundle\TestTaskOroBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/issue")
 */
class IssueController extends Controller
{
    /**
     * @Route("/", name="magecore_testtaskoro_issue")
     * @Template
     * @Acl(
     *     id="testtaskoro.issue_view",
     *     type="entity",
     *     class="MagecoreTestTaskOroBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction(){
        return array('entity_class'=>'MagecoreTestTaskOroBundle\Entity\Issue');
    }

}