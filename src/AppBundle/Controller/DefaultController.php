<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Review;
use AppBundle\Services\ScoreCalculator\ExpressionBuilder;
use AppBundle\Services\ScoreCalculator\ScoreCalculationService;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\DataGridBundle\Grid\Action\RowAction;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // Creates simple grid based on your entity (ORM)
        $source = new Entity('AppBundle:Review');

        $gridManager = $this->get('grid.manager');

        $grid = $gridManager->createGrid();
        $grid->setSource($source);
        $grid->setPersistence(true);
        $grid->setLimits(array(3, 10, 15));
        $grid->setDefaultPage(1);

        $myRowAction = new RowAction('Delete', 'delete_review', true, '_self');
        $grid->addRowAction($myRowAction);

        $grid->addMassAction(new DeleteMassAction());

        if ($gridManager->isReadyForRedirect()) {
            return new RedirectResponse($gridManager->getRouteUrl());
        } else {

            return $this->render('default/index.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'grid' => $grid
            ));
        }
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/calculate-score", name="calculate_score")
     */
    public function calculateScoreAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reviews = $em->getRepository('AppBundle:Review')->findAll();

        foreach ($reviews as $review) {
            $scoreService = new ScoreCalculationService($review, $this->getDoctrine());
            $scoreService->run();
            
            $review->setTotalScore($scoreService->getTotalScore());
            $review->setScoreDescription($scoreService->getScoreDescription());
            $em->flush();
        }
        return $this->redirect('/');
    }
}
