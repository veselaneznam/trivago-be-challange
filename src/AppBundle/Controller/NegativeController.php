<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/17/16
 * Time: 4:56 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Negative;
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

class NegativeController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     * @Route ("/negative")
     */
    public function indexAction(Request $request)
    {

        $sourceNegative = new Entity('AppBundle:Negative');

        $gridManager = $this->get('grid.manager');

        $gridNegative = $gridManager->createGrid();
        $gridNegative = $this->setUpNegativeGrid($gridNegative, $sourceNegative, 'negative');

        if ($gridManager->isReadyForRedirect()) {
            return new RedirectResponse($gridManager->getRouteUrl());
        } else {
            return $this->render('Negative/grid.html.twig',
                array(
                    'gridNegative' => $gridNegative,
                ));
        }
    }
    
    /**
     * @Route ("/negative/add", name="add_negative")
     * @return Response
     */
    public function createAction(Request $request)
    {
        $negative = new Negative();

        $form = $this->createForm(new \AppBundle\Form\AddNegativeType(), $negative, [
            'action' => $this->generateUrl('add_negative'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $negatives = explode(',', $form->get('negative')->getData());
            foreach ($negatives as $data) {
                $em = $this->getDoctrine()->getManager();
                $negative = new Negative();
                $data = str_replace("'", '', $data);
                $data = str_replace('"', '', $data);
                $negative->setNegative($data);
                $em->persist($negative);
                $em->flush();
            }
            ExpressionBuilder::rebuildExpression($this->getDoctrine());
            return $this->redirect('/negative');
        }
        return $this->render('Negative/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => 'You can use comma separated string in order to add more negatives',
            'title' => 'Create Negative'
        ));
    }

    /**
     * @Route ("/negative/delete/{id}", name="delete_negative")
     */
    public function deleteAction(Request $request)
    {
        $negativeId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $negative = $em->getRepository('AppBundle:Negative')->find($negativeId);
        
        $em->remove($negative);
        $em->flush();
        ExpressionBuilder::rebuildExpression($this->getDoctrine());
        return $this->redirect('/negative');
    }

    /**
     * @Route ("/negative/edit/{id}", name="edit_negative")
     */
    public function editAction(Request $request)
    {
        $negativeId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $negative = $em->getRepository('AppBundle:Negative')->find($negativeId);

        if (!$negative) {
            throw $this->createNotFoundException(
                'No Negative found for id ' . $negativeId
            );
        }

        $form = $this->createForm(new \AppBundle\Form\AddNegativeType(), $negative, [
            'action' => $this->generateUrl('edit_negative', ['id' => $negativeId]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->get('negative')->getData();
            if(strpos($data, ',') !== false) {
                throw new \ErrorException('You are not allowed to add more than one negatives');
            }
            $negative->setNegative($data);
            $em->flush();
            ExpressionBuilder::rebuildExpression($this->getDoctrine());
            return $this->redirect('/negative');
        }

        return $this->render('Negative/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => false,
            'title' => 'Edit Negative'
        ));
    }

    /**
     * @param $gridNegative
     * @param $sourceCriteria
     * @return mixed
     */
    private function setUpNegativeGrid($gridNegative, $sourceCriteria, $name)
    {
        $gridNegative->setSource($sourceCriteria);
        $gridNegative->setPersistence(true);
        $gridNegative->setLimits(array(10, 20, 30));
        $gridNegative->setDefaultPage(1);
        $myRowAction = new RowAction('Edit', 'edit_' . $name);
        $gridNegative->addRowAction($myRowAction);

        $myRowAction = new RowAction('Delete', 'delete_' . $name, true, '_self');
        $gridNegative->addRowAction($myRowAction);

        // Add a delete mass action
        $gridNegative->addMassAction(new DeleteMassAction());
        return $gridNegative;
    }
}