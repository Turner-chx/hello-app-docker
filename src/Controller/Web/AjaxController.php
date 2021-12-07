<?php


namespace App\Controller\Web;


use App\Entity\Source;
use App\Form\SavArticleType;
use App\Service\AutocompleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @param Request $request
     * @param AutocompleteService $autocompleteService
     * @return JsonResponse
     *
     * @Route("/autocomplete", name="ajax_autocomplete")
     */
    public function autocompleteAction(Request $request, AutocompleteService $autocompleteService): JsonResponse
    {
        $q = strtoupper(trim(str_replace(['.', '/', '-', ' '], '', $request->query->get('q'))));
        $request->query->set('q', $q);
        $source = $this->getDoctrine()->getRepository(Source::class)
            ->findOneBy([
                'name' => $request->getHttpHost()
            ]);
        if (!$source) {
            $source = $this->getDoctrine()->getRepository(Source::class)
                ->findOneBy([
                    'defaultSource' => true
                ]);
        }
        $result = $autocompleteService->getAutocompleteResults($request, SavArticleType::class, $source);
        return new JsonResponse($result);
    }

}