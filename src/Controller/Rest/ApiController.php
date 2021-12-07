<?php


namespace App\Controller\Rest;


use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/get-list-sav/{dealerCode}", name="get_list")
     */
    /**
     * @Get(
     *     path = "/get-list-sav/{dealerCode}",
     *     name = "get_list"
     * )
     */
    public function getListFromDealer($dealerCode)
    {
        return new JsonResponse(['coucou']);
    }
}