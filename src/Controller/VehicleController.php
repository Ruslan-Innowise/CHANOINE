<?php

namespace App\Controller;

use App\Entity\Vehicles;
use App\Repository\VehiclesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    /**
     * @Route("/vehicle", name="vehicle")
     *
     * @return Response
     */
    public function appDefaultAction(): Response
    {
        $vehicles = $this
            ->getDoctrine()
            ->getRepository(Vehicles::class)
            ->findAll();

        return $this->render('app/index.html.twig',
            [
                'vehicles' => $vehicles
            ]
        );
    }
}