<?php


namespace App\Service;


use App\Entity\Source;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AutocompleteService
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param FormFactoryInterface $formFactory
     * @param ManagerRegistry $doctrine
     */
    public function __construct(FormFactoryInterface $formFactory, ManagerRegistry $doctrine)
    {
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     * @param string|FormTypeInterface $type
     *
     * @return array
     */
    public function getAutocompleteResults(Request $request, $type, Source $source)
    {
        $form = $this->formFactory->create($type);
        $fieldOptions = $form->get($request->get('field_name'))->getConfig()->getOptions();

        /** @var EntityRepository $repo */
        $repo = $this->doctrine->getRepository($fieldOptions['class']);

        $term = $request->get('q');

        $countQB = $repo->createQueryBuilder('e');
        $countQB
            ->select($countQB->expr()->count('e'));
        if (!is_array($fieldOptions['property'])) {
            $countQB
                ->where("replace(replace(replace(replace(e." . $fieldOptions['property'] . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
        } else {
            foreach ($fieldOptions['property'] as $key => $property) {
                if (0 === $key) {
                    $countQB->where("replace(replace(replace(replace(e." . $property . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
                } else {
                    $countQB->orWhere("replace(replace(replace(replace(e." . $property . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
                }
            }
        }
        if (null !== $source && $source->getGammes()->count() > 0) {
            $countQB
                ->innerJoin('e.gamme', 'g')
                ->innerJoin('g.sources', 's')
                ->andWhere('s.id = :id')
                ->setParameter('id', $source->getId())
            ;
        }
        $countQB
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('e.status', 'DESC');

        $maxResults = $fieldOptions['page_limit'];
        $offset = ($request->get('page', 1) - 1) * $maxResults;

        $resultQb = $repo->createQueryBuilder('e');
        if (!is_array($fieldOptions['property'])) {
            $resultQb
                ->where("replace(replace(replace(replace(e." . $fieldOptions['property'] . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
        } else {
            foreach ($fieldOptions['property'] as $key => $property) {
                if (0 === $key) {
                    $resultQb->where("replace(replace(replace(replace(e." . $property . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
                } else {
                    $resultQb->orWhere("replace(replace(replace(replace(e." . $property . ", '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term");
                }
            }
        }
        if (null !== $source && $source->getGammes()->count() > 0) {
            $resultQb
                ->innerJoin('e.gamme', 'g')
                ->innerJoin('g.sources', 's')
                ->andWhere('s.id = :id')
                ->setParameter('id', $source->getId())
            ;
        }

        $resultQb
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('e.status', 'DESC')
            ->setMaxResults($maxResults)
            ->setFirstResult($offset);
        if (is_callable($fieldOptions['callback'])) {
            $cb = $fieldOptions['callback'];

            $cb($countQB, $request);
            $cb($resultQb, $request);
        }

        $count = $countQB->getQuery()->getSingleScalarResult();
        $paginationResults = $resultQb->getQuery()->getResult();

        $result = ['results' => null, 'more' => $count > ($offset + $maxResults)];

        $accessor = PropertyAccess::createPropertyAccessor();

        $result['results'] = array_map(function ($item) use ($accessor, $fieldOptions) {
            return ['id' => $accessor->getValue($item, $fieldOptions['primary_key']), 'text' => $item->__toString()];
        }, $paginationResults);

        return $result;
    }
}