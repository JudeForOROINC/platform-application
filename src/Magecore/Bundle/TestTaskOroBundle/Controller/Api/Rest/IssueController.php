<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 22.07.15
 * Time: 12:00
 */
// src/InventoryBundle/Controller/VehicleController.php
namespace Magecore\Bundle\TestTaskOroBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;
use Magecore\Bundle\TestTaskOroBundle\Form\Type\IssueType;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

/**
 * @RouteResource("issue")
 * @NamePrefix("magecore_testtaskoro_api_")
 *
 */
class IssueController extends RestController
{
    /**
     * @ApiDoc(
     *     description="Delete IssueEntity",
     *     resource=true
     * )
     * @Acl(
     *      id="magecore_testtaskoro.issue_delete",
     *      type="entity",
     *      class="MagecoreTestTaskOroBundle:Issue",
     *      permission="DELETE"
     * )
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    public function getForm()
    {
        return $this->get('magecore_testtaskoro.form.type.issue.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->get('magecore_testtaskoro.form.handler.issue.api');
    }

    public function getManager()
    {
        return $this->get('magecore_testtaskoro.issue_manager.api');
    }

    /**
    * @ApiDoc(
    *      description="Create new account",
    *      resource=true
    * )
    * @AclAncestor("magecore_testtaskoro.issue_create")
    */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }


}
