<?php

namespace App\Controller\Api;

use App\Entity\Beer;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class BeerController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/beer/{id}")
     * @param int $id
     *
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function getBeerAction(int $id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Beer::class);
        $beer = $repository->find($id);
        $jsonBeer = $serializer->serialize(
            $beer,
            'json',
            [
                'groups' => 'show_beer',
                'json_encode_options' => JSON_UNESCAPED_SLASHES
            ]
        );

        return $this->handleView($this->view($jsonBeer));
    }

    /**
     * @Rest\Get("/beer/pages/{page}/limits/{limit}")
     *
     * @param int $page
     * @param int $limit
     *
     * @return Response
     */
    public function getBeersPaginatedAction(int $page, int $limit, Request $request): Response
    {
        $filters = $request->query->all() ?: [];
        $repository = $this->getDoctrine()->getRepository(Beer::class);
        $result = $repository->findAllFilteredPaginated($limit, $this->getOffset($limit, $page), $filters);

        return $this->handleView($this->view($result));
    }

    /**
     * @param int $limit
     * @param int $page
     *
     * @return int
     */
    private function getOffset(int $limit, int $page): int
    {
        return ($page - 1) * $limit;
    }
}
