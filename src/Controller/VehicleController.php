<?php

namespace App\Controller;

use App\Entity\Vehicles;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    /**
     * @Route("/vehicle", name="vehicle")
     *
     * @return Response
     */
    public function appDefaultAction(Request $request): Response
    {
        $params = [];

        count((array)$request->query->get('brands')) === 0 ?: $params['brand'] = $request->query->get('brands');
        count((array)$request->query->get('models')) === 0 ?: $params['model'] = $request->query->get('models');
        !(string)$request->query->get('budget') ?: $params[$request->query->get('budget')] = [(integer)$request->query->get('price-min'), (integer)$request->query->get('price-max')];

        $orderBy = explode('-', (string)$request->query->get('order'));


        $filters = [
            'brands' => [],
            'models' => [],
            'energy' => [],
            'price_min' => null,
            'price_max' => null
        ];

        $results =  $this->getDoctrine()
            ->getRepository(Vehicles::class)
            ->findByAllFields(
                $request->query->get('page') ?? 1,
                $params,
                [$orderBy[0] ?? 'id' => $orderBy[1] ?? 'asc']);

        //TODO Refactoring (Add Service)
        //TODO Edit Get params filter
        $vehiclesFilters =  $this->getDoctrine()
            ->getRepository(Vehicles::class)
            ->findAll();

        foreach ($vehiclesFilters as $vehicle) {
            if (!in_array($vehicle->getBrand(), $filters['brands'], true)) {
                $filters['brands'][] = $vehicle->getBrand();
            }
            if (!in_array($vehicle->getModel(), $filters['models'], true)) {
                $filters['models'][] = $vehicle->getModel();
            }
            if (!in_array($vehicle->getEnergy(), $filters['energy'], true)) {
                $filters['energy'][] = $vehicle->getEnergy();
            }
            $filters['price_min'] = $filters['price_min'] === null ? $vehicle->getPrice() : min($filters['price_min'], $vehicle->getPrice());
            $filters['price_max'] = max($filters['price_max'], $vehicle->getPrice());
//            $filters['price_monthly_min'] = $filters['price_monthly_min'] === null ? $vehicle->getPriceMonthly() : min($filters['price_monthly_min'], $vehicle->getPriceMonthly());
//            $filters['price_monthly_max'] = max($filters['price_monthly_max'], $vehicle->getPriceMonthly());
        }

        return $this->render('app/index.html.twig',
            [
                'vehicles' => $results,
                'filters' => $filters,
            ]
        );
    }
}