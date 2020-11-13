<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Voiture;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(Voiture $voiture = null , Request $request) 
    {
        $form = $this->createFormBuilder()
        ->add('dateDebut', DateTimeType::class)
        ->add('dateFin', DateTimeType::class)
        ->add('verifier', SubmitType::class)
        ->getForm();

        $data = null;
        $carId = null;
        $dateStart = null;
        $dateEnd = null;
        $voituredispo = null;
        $bookable = null;
        $allresa = null;
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager(); 
          $data = $form->getData();
          $dateStart = $data["dateDebut"];
          $dateEnd = $data["dateFin"];
          $allresa = $em->getRepository(Reservation::class)->findAll();
          $voituredispo = $em->getRepository(Voiture::class)->findByAvailable($dateStart, $dateEnd, $bookable);
        //   $carId = $voituredispo->getId();
        }
        // if (isset($carId)) {
            
        //     $reservation = new Reservation();
        //     $form = $this->createForm(ReservationType::class, $reservation);
        //     $form->handleRequest($request);
        //     $reservation->setVoiture($carId);
        //     $reservation->setDateStart($dateStart);
        //     $reservation->setDateEnd($dateEnd);
        //     $entityManager = $this->getDoctrine()->getManager(); 
        //     $entityManager->persist($reservation);
        //     $entityManager->flush();
        // }


        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            "data" => $data,
            "dateStart" => $dateStart,
            "dateEnd" => $dateEnd,
            "voituresdispo" => $voituredispo,
            "bookable" => $bookable,
            "carid" => $carId,
            "allresa" => $allresa,
        ]);
    }
    /**
     * @Route("/voiture/{id}/{start}/{end}", name="index_show", methods={"GET", "POST"}) // récup data depuis url GET
     */
    public function confirmResa(Voiture $voiture, $start, $end, Request $request): Response
    {
        $dateStart = $start;
        $dateEnd = $end;

        $form = $this->createFormBuilder()
        ->add('submit', SubmitType::class)
        ->getForm();
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager(); 
            $reservation = new Reservation();
            $reservation->setVoiture($voiture);
            $reservation->setDateStart(new \DateTime($dateStart));
            $reservation->setDateEnd(new \DateTime($dateEnd));

            $entityManager->persist($reservation);
            $entityManager->flush();

        return $this->render('index/show.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
            'resa' => true,
            "dateEnd" => $dateEnd,
            "dateStart" => $dateStart,
        ]);  
        }
        // $bookable = null;

        // 
        // $entityManager->getRepository(Voiture::class)->findByAvailable($dateStart, $dateEnd, $bookable);

        

        return $this->render('index/show.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
            'resa' => false,
            "dateEnd" => $dateEnd,
            "dateStart" => $dateStart,
        ]);        
    }

}
            