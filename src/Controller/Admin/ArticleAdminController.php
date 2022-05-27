<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Brand;
use App\Entity\Gamme;
use App\Entity\ProductType;
use App\Form\FileType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class ArticleAdminController extends CRUDController
{
    public function importArticlesAction(Request $request)
    {
        set_time_limit(0);
        ini_set('max_execution_time', '3600');
        $form = $this->createForm(FileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->files->get('file');
            if (isset($data['file'])) {
                $i = 0;
                $batchSize = 500;
                $manager = $this->getDoctrine()->getManager();
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $data['file']['file'];
                $readerSpreadsheet = IOFactory::createReader('Xlsx');
                $spreadsheet = $readerSpreadsheet->load($uploadedFile->getPathname());
                $productsArray = $spreadsheet->getActiveSheet()->toArray();
                unset($productsArray[0], $productsArray[1]);

                foreach ($productsArray as $productItem) {
                    if(null === $productItem[0] && null === $productItem[5] && null === $productItem[1]) {
                        break;
                    }
                    $gamme = null;
                    $productType = trim($productItem[4]) ?? '';
                    $brand = null;
                    $ref = trim($productItem[0]);
                    $des = trim($productItem[1]);
                    $desAbr = trim($productItem[2]);
                    $ean = trim($productItem[5]);
                    $brandLama = substr(trim($productType), -2);
                    $productTypeLama = substr(trim($productType) ?? '', 0, 3);
                    $gammeLama = trim($productItem[3]);
                    $active = trim($productItem[6]) === 'Oui';
                    $brand = $manager->getRepository(Brand::class)
                        ->findOneBy([
                            'codeLama' => $brandLama
                        ]);
                    if (null === $brand) {
                        $brand = new Brand();
                        $brand->setStatus(true);
                        $brand->setBrand($brandLama);
                        $brand->setCodeLama($brandLama);
                        $manager->persist($brand);
                        $manager->flush();
                    }
                    $productType = $manager->getRepository(ProductType::class)
                        ->findOneBy([
                            'codeLama' => $productTypeLama
                        ]);

                    if (null === $productType) {
                        $productType = new ProductType();
                        $productType->setCodeLama($productTypeLama);
                        $productType->setType($productTypeLama);
                        $manager->persist($productType);
                        $manager->flush();
                    }
                    if (null !== $gammeLama) {
                        $gamme = $manager->getRepository(Gamme::class)
                            ->findOneBy([
                                'gamme' => $gammeLama
                            ]);
                        if (null === $gamme) {
                            $gamme = new Gamme();
                            $gamme->setGamme($gammeLama);
                            $manager->persist($gamme);
                            $manager->flush();
                        }
                    }
                    $article = $manager->getRepository(Article::class)
                        ->findOneBy([
                            'reference' => $ref
                        ]);
                    if (null !== $ref && null !== $des && null !== $desAbr) {
                        if (null === $article) {
                            $article = new Article();
                        }
                        $article->setReference($ref);
                        $article->setDesignation($des);
                        $article->setDesignationAbridged($desAbr);
                        $article->setEan($ean);
                        $article->setStatus($active);
                        if (null !== $gamme) {
                            $article->setGamme($gamme);
                        }
                        if (null !== $productType) {
                            $article->setProductType($productType);
                        }
                        if (null !== $brand) {
                            $article->setBrand($brand);
                        }

                        $manager->persist($article);
                    }
                    if ($i % $batchSize === 0) {
                        $manager->flush();
                    }
                    $i++;
                }
            }
            $manager->flush();
            $this->addFlash('success', $i . ' produits mis à jour');
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('admin/article/import_articles.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
