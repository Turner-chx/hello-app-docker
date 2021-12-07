<?php


namespace App\Controller\Web;


use App\Entity\Source;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function index(Request $request)
    {
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
        return $this->render('home/index.html.twig', [
            'source' => $source
        ]);
    }
}