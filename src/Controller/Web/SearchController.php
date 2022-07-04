<?php


namespace App\Controller\Web;


use App\Entity\Sav;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SearchController extends AbstractController
{
    public function savSearch(Request $request, Pool $pool): Response
    {
        $savGroup = null;
        $groups = $pool->getDashboardGroups();
        foreach ($groups as $key => $group) {
            if ($key === 'admin.menu.sav') {
                foreach ($group['items'] as $id => $item) {
                    if ($item->getCode() !== 'admin.sav') {
                        unset($group['items'][$id]);
                    }
                }
                $savGroup = $group;
            }
        }

        $results = $this->getDoctrine()->getRepository(Sav::class)->searchSav($request->get('q'));

        return $this->render('admin/search/search.html.twig', [
            'admin_pool' => $pool,
            'query' => $request->get('q'),
            'group' => $savGroup,
            'results' => $results,
        ]);
    }

}