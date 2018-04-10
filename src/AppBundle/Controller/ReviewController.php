<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 1:56 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Review;
use AppBundle\Services\Import\FileProcessing\Csv;
use AppBundle\Services\Import\ReviewImportService;
use AppBundle\Services\ScoreCalculator\ScoreCalculationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route ("/reviews")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Review');
        /**
         * @var Review $reviews
         */
        $reviews = $repository->findAll();

        foreach ($reviews as $review) {

            list($totalScore, $scoreDescription) = $this->calculateScore($review);
            $review->setTotalScore($totalScore);
            $review->setScoreDescription($scoreDescription);
        }

        return $this->render('Review/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
            'reviews' => $reviews
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("/reviews/add", name="add_mass_review")
     */
    public function addAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'File to Submit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            try {
                $file = $form->get('submitFile');
                list($counter, $warnings) = $this->saveReviews($request, $file->getData()->getRealPath());

                $successMessage = $counter . ' was successfully saved';
                $this->get('session')->getFlashBag()->add('notice', $successMessage);

                if (!empty($warnings)) {
                    $warningsMessage = 'Some problems during import: ' . implode('<br />', $warnings);
                    $this->get('session')->getFlashBag()->add('notice', $warningsMessage);
                }
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->set('error', $e->getMessage());
            }

            return $this->redirect('/');
        }

        return $this->render('Review/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route ("/review/delete/{id}", name="delete_review")
     */
    public function deleteAction(Request $request)
    {
        $reviewId = (int)$request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('AppBundle:Review')->find($reviewId);

        $em->remove($review);
        $em->flush();
        return $this->redirect('/');
    }

    /**
     * @param $filePath
     * @return array
     */
    private function saveReviews(Request $request, $filePath)
    {
        $registry = $this->getDoctrine();

        $importService = new ReviewImportService($registry);
        $csvFileProcessor = new Csv($importService);
        return $csvFileProcessor->process($filePath);
     }

    /**
     * @param Review $review
     * @return array
     */
    private function calculateScore(Review $review)
    {
        $scoreService = new ScoreCalculationService($review, $this->getDoctrine());
        return $scoreService->run();
    }
}