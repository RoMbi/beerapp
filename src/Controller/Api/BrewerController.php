<?php

namespace App\Controller\Api;

use App\Entity\Brewer;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class BrewerController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/brewers")
     *
     * @return Response
     */
    public function getBrewersAction(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Brewer::class);
        $result = $repository->findAllWithBeerCount();

        return $this->handleView($this->view($result));
    }
}
