<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 25.07.15
 * Time: 12:06
 */
// src/InventoryBundle/Controller/Api/Rest/VehicleController.php
namespace Magecore\Bundle\TestTaskOroBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;

/**
 * @RouteResource("issue")
 * @NamePrefix("magecore_testtaskoro_api_")
 */
class IssueController extends RestController
{
    /**
     * @Acl(
     *      id="magecore_testtaskoro.issue_delete",
     *      type="entity",
     *      class="MagecoreTestTaskOroBundle:Issue",
     *      permission="DELETE"
     * )
     * @ApiDoc(
     *      description="Get all task items",
     *      resource=true
     * )
     * @AclAncestor("magecore_testtaskoro_issue_delete")
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    public function getForm()
    {
    }

    public function getFormHandler()
    {
    }

    public function getManager()
    {
        return $this->get('magecore_testtaskoro.issue_manager.api');
    }
}