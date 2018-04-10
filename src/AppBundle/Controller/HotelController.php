<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/19/16
 * Time: 8:55 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Hotel;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;

class HotelController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     * @Route ("/hotel")
     */
    public function indexAction(Request $request)
    {

        $sourceHotel = new Entity('AppBundle:Hotel');

        $gridManager = $this->get('grid.manager');

        $gridHotel = $gridManager->createGrid();
        $gridHotel = $this->setUpCriteriaGrid($gridHotel, $sourceHotel, 'hotel');

        if ($gridManager->isReadyForRedirect()) {
            return new RedirectResponse($gridManager->getRouteUrl());
        } else {
            return $this->render('Hotel/grid.html.twig',
                array(
                    'gridHotel' => $gridHotel,
                ));
        }
    }

    /**
     * @Route ("/hotel/add", name="add_hotel")
     * @return Response
     */
    public function createAction(Request $request)
    {
        $hotel = new Hotel();

        $form = $this->createForm(new \AppBundle\Form\AddHotelType(), $hotel, [
            'action' => $this->generateUrl('add_hotel'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $hotels = explode(',', $form->get('name')->getData());
            foreach ($hotels as $data) {
                $em = $this->getDoctrine()->getManager();
                $hotel = new Hotel();
                $hotel->setName(trim($data));
                $em->persist($hotel);
                $em->flush();
            }

            return $this->redirect('/hotel');
        }
        return $this->render('Hotel/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => 'You can use comma separated string in order to add more hotels',
            'title' => 'Create Hotel'
        ));
    }

    /**
     * @Route ("/hotel/delete/{id}", name="delete_hotel")
     */
    public function deleteAction(Request $request)
    {
        $hotelId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository('AppBundle:Hotel')->find($hotelId);

        $em->remove($hotel);
        $em->flush();
        return $this->redirect('/hotel');
    }

    /**
     * @Route ("/hotel/edit/{id}", name="edit_hotel")
     */
    public function editAction(Request $request)
    {
        $hotelId = (int) $request->get('id', null);
        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository('AppBundle:Hotel')->find($hotelId);

        if (!$hotel) {
            throw $this->createNotFoundException(
                'No Hotel found for id ' . $hotelId
            );
        }

        $form = $this->createForm(new \AppBundle\Form\AddHotelType(), $hotel, [
            'action' => $this->generateUrl('edit_hotel', ['id' => $hotelId]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->get('name')->getData();
            if(strpos($data, ',') !== false) {
                throw new \ErrorException('You are not allowed to add more than one hotels');
            }
            $hotel->setName($data);
            $em->flush();

            return $this->redirect('/hotel');
        }

        return $this->render('Hotel/add.html.twig', array(
            'form' => $form->createView(),
            'helptext' => false,
            'title' => 'Edit Hotel'
        ));
    }

    /**
     * @param $gridHotel
     * @param $sourceCriteria
     * @return mixed
     */
    private function setUpCriteriaGrid($gridHotel, $sourceCriteria, $name)
    {
        $gridHotel->setSource($sourceCriteria);
        $gridHotel->setPersistence(true);
        $gridHotel->setLimits(array(10, 20, 30));
        $gridHotel->setDefaultPage(1);
        $myRowAction = new RowAction('Edit', 'edit_' . $name);
        $gridHotel->addRowAction($myRowAction);

        $myRowAction = new RowAction('Delete', 'delete_' . $name, true, '_self');
        $gridHotel->addRowAction($myRowAction);

        $gridHotel->addMassAction(new DeleteMassAction());
        return $gridHotel;
    }
}