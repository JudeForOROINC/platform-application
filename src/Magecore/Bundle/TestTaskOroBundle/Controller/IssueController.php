<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 22.07.15
 * Time: 12:00
 */
// src/InventoryBundle/Controller/VehicleController.php
namespace Magecore\Bundle\TestTaskOroBundle\Controller;

use Magecore\Bundle\TestTaskOroBundle\Entity\Issue;
use Magecore\Bundle\TestTaskOroBundle\Form\Type\IssueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
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
     * @Route("/create", name="magecore_testtaskoro.issue_create")
     * @Template("MagecoreTestTaskOroBundle:Issue:update.html.twig")
     * @Acl(
     *     id="magecore_testtaskoro.issue_create",
     *     type="entity",
     *     class="MagecoreTestTaskOroBundle:Issue",
     *     permission="CREATE"
     * )
     */
    public function createAction(Request $request)
    {
        return $this->update(new Issue(), $request);
    }

    /**
     * @Route("/update/{id}", name="magecore_testtaskoro.issue_update", requirements={"id":"\d+"}, defaults={"id":0})
     * @Template()
     * @Acl(
     *     id="magecore_testtaskoro.issue_update",
     *     type="entity",
     *     class="MagecoreTestTaskOroBundle:Issue",
     *     permission="EDIT"
     * )
     */
    public function updateAction(Issue $issue, Request $request)
    {
        return $this->update($issue, $request);
    }

    private function update(Issue $issue, Request $request)
    {
        $form = $this->get('form.factory')->create('magecore_testtaskoro_issue', $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                array(
                    'route' => 'magecore_testtaskoro.issue_update',
                    'parameters' => array('id' => $issue->getId()),
                ),
                array('route' => 'magecore_testtaskoro_issue'),
                $issue
            );
        }

        return array(
            'entity' => $issue,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="magecore_testtaskoro.issue_view", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("magecore_testtaskoro_issue_view")
     */
    public function viewAction(Issue $issue)
    {
        return array('entity' => $issue);
    }



}