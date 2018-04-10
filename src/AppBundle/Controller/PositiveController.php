<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/17/16
 * Time: 4:56 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Positive;
use AppBundle\Services\ScoreCalculator\ExpressionBuilder;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;

class PositiveController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     * @Route ("/positive")
     */
    public function indexAction(Request $request)
    {

        $sourcePositive = new Entity('AppBundle:Positive');

        $gridManager = $this->get('grid.manager');

        $gridPositive = $gridManager->createGrid();
        $gridPositive = $this->setUpPositiveGrid($gridPositive, $sourcePositive, 'positive');

        if ($gridManager->isReadyForRedirect()) {
            return new RedirectResponse($gridManager->getRouteUrl());
        } else {
            return $this->render('Positive/grid.html.twig',
                array(
                    'gridPositive' => $gridPositive,
                ));
        }
    }


    /**
     * @Route ("/positive/add", name="add_positive")
     * @return Response
     */
    public function createAction(Request $request)
    {
        $positive = new Positive();

        $form = $this->createForm(new \AppBundle\Form\AddPositiveType(), $positive, [
            'action' => $this->generateUrl('add_positive'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $positives = explode(',', $form->get('positive')->getData());
            foreach ($positives as $data) {
                $em = $this->getDoctrine()->getManager();
                $positive = new Positive();
                $data = str_replace("'", '', $data);
                $data = str_replace('"', '', $data);
                $positive->setPositive($data);
                $em->persist($positive);
                $em->flush();
            }

            ExpressionBuilder::rebuildExpression($this->getDoctrine());

            return $this->redirect('/positive');
        }
        return $this->render('Positive/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => 'You can use comma separated string in order to add more positives',
            'title' => 'Create Positive'
        ));
    }

    /**
     * @Route ("/positive/delete/{id}", name="delete_positive")
     */
    public function deleteAction(Request $request)
    {
        $positiveId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $positive = $em->getRepository('AppBundle:Positive')->find($positiveId);
        $em->remove($positive);
        $em->flush();
        ExpressionBuilder::rebuildExpression($this->getDoctrine());
        return $this->redirect('/positive');
    }

    /**
     * @Route ("/positive/edit/{id}", name="edit_positive")
     */
    public function editAction(Request $request)
    {
        $positiveId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $positive = $em->getRepository('AppBundle:Positive')->find($positiveId);

        if (!$positive) {
            throw $this->createNotFoundException(
                'No Positive found for id '.$positiveId
            );
        }

        $form = $this->createForm(new \AppBundle\Form\AddPositiveType(), $positive, [
            'action' => $this->generateUrl('edit_positive', ['id' => $positiveId]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->get('positive')->getData();
            if(strpos($data, ',') !== false) {
                throw new \ErrorException('You are not allowed to add more than one negatives');
            }
            $positive->setPositive($data);
            $em->flush();
            ExpressionBuilder::rebuildExpression($this->getDoctrine());
            return $this->redirect('/positive');
        }


        return $this->render('Positive/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => false,
            'title' => 'Edit Positive'
        ));
    }

    /**
     * @param $gridPositive
     * @param $sourceCriteria
     * @return mixed
     */
    private function setUpPositiveGrid($gridPositive, $sourceCriteria, $name)
    {
        $gridPositive->setSource($sourceCriteria);
        $gridPositive->setPersistence(true);
        $gridPositive->setLimits(array(3, 10, 15));
        $gridPositive->setDefaultPage(1);
        $myRowAction = new RowAction('Edit', 'edit_' . $name);
        $gridPositive->addRowAction($myRowAction);

        $myRowAction = new RowAction('Delete', 'delete_' . $name, true, '_self');
        $gridPositive->addRowAction($myRowAction);
        // Add a delete mass action
        $gridPositive->addMassAction(new DeleteMassAction());
        
        return $gridPositive;
    }
}