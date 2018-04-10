<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Criteria;

use AppBundle\Services\ScoreCalculator\ExpressionBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/1/16
 * Time: 3:42 PM
 */
class CriteriaController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route ("/criteria")
     */
    public function indexAction(Request $request)
    {
        // Creates simple grid based on your entity (ORM)
        $sourceCriteria = new Entity('AppBundle:Criteria');

        $gridManager = $this->get('grid.manager');

        $gridCriteria = $gridManager->createGrid();
        $gridCriteria = $this->setUpCriteriaGrid($gridCriteria, $sourceCriteria, 'criteria');


        if ($gridManager->isReadyForRedirect()) {
            return new RedirectResponse($gridManager->getRouteUrl());
        } else {
            return $this->render('Criteria/grid.html.twig',
                array(
                    'gridCriteria' => $gridCriteria,
                ));
        }
    }

    /**
     * @Route ("/criteria/add", name="add_criteria")
     * @return Response
     */
    public function createAction(Request $request)
    {
        $criteria = new Criteria();

        $form = $this->createForm(new \AppBundle\Form\AddCriteriaType(), $criteria, [
            'action' => $this->generateUrl('add_criteria'),
            'method' => 'POST'
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $criteria = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($criteria);
            $em->flush();
            ExpressionBuilder::rebuildExpression($this->getDoctrine());
            return $this->redirect('/criteria');
        }
        return $this->render('Criteria/add.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Create Positive'
        ));
    }

    /**
     * @Route ("/criteria/delete/{id}", name="delete_criteria")
     */
    public function deleteCriteriaAction(Request $request)
    {
        $criteriaId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $criteria = $em->getRepository('AppBundle:Criteria')->find($criteriaId);
        $em->remove($criteria);
        $em->flush();
        ExpressionBuilder::rebuildExpression($this->getDoctrine());
        return $this->redirect('/criteria');
    }

    /**
     * @Route ("/criteria/edit/{id}", name="edit_criteria")
     */
    public function editCriteriaAction(Request $request)
    {
        $criteriaId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $criteria = $em->getRepository('AppBundle:Criteria')->find($criteriaId);

        if (!$criteria) {
            throw $this->createNotFoundException(
                'No criteria found for id '.$criteriaId
            );
        }
        
        $form = $this->createForm(new \AppBundle\Form\AddCriteriaType(), $criteria, [
            'action' => $this->generateUrl('edit_criteria', ['id' => $criteriaId]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $criteria->setName($form->get('name')->getData());
            $criteria->setAlternativeName($form->get('alternative_name')->getData());
            $em->flush();
            ExpressionBuilder::rebuildExpression($this->getDoctrine());
            return $this->redirect('/criteria');
        }

        
        return $this->render('Criteria/add.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Edit Criteria'
        ));
    }

    /**
     * @param $gridCriteria
     * @param $sourceCriteria
     * @return mixed
     */
    private function setUpCriteriaGrid($gridCriteria, $sourceCriteria, $name)
    {
        $gridCriteria->setSource($sourceCriteria);
        $gridCriteria->setPersistence(true);
        $gridCriteria->setLimits(array(3, 10, 15));
        $gridCriteria->setDefaultPage(1);
        $myRowAction = new RowAction('Edit', 'edit_' . $name);
        $gridCriteria->addRowAction($myRowAction);

        $myRowAction = new RowAction('Delete', 'delete_' . $name, true, '_self');
        $gridCriteria->addRowAction($myRowAction);

        // Add a delete mass action
        $gridCriteria->addMassAction(new DeleteMassAction());
        return $gridCriteria;
    }
}