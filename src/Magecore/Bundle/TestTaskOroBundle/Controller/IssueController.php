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
use Symfony\Component\HttpFoundation\Request;
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
    /**
     * @Route("/create", name="inventory.vehicle_create")
     * @Template("InventoryBundle:Vehicle:update.html.twig")
     * @Acl(
     *     id="inventory.vehicle_create",
     *     type="entity",
     *     class="InventoryBundle:Vehicle",
     *     permission="CREATE"
     * )
     */
    public function createAction(Request $request)
    {
        return $this->update(new Vehicle(), $request);
    }

    /**
     * @Route("/update/{id}", name="inventory.vehicle_update", requirements={"id":"\d+"}, defaults={"id":0})
     * @Template()
     * @Acl(
     *     id="inventory.vehicle_update",
     *     type="entity",
     *     class="InventoryBundle:Vehicle",
     *     permission="EDIT"
     * )
     */
    public function updateAction(Vehicle $vehicle, Request $request)
    {
        return $this->update($vehicle, $request);
    }

    private function update(Vehicle $vehicle, Request $request)
    {
        $form = $this->get('form.factory')->create('inventory_vehicle', $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vehicle);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                array(
                    'route' => 'inventory.vehicle_update',
                    'parameters' => array('id' => $vehicle->getId()),
                ),
                array('route' => 'inventory.vehicle_index'),
                $vehicle
            );
        }

        return array(
            'entity' => $vehicle,
            'form' => $form->createView(),
        );
    }


}