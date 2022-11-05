<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    public function __construct(
        private SeriesRepository $seriesRepository,
        private EntityManagerInterface $entityManager,
    ) {  
    }

    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [
            'series' => $seriesList,
        ]);
    }

    #[Route('/series/create', name: 'app_series_form',methods: ['GET'])]
    public function addSeriesForm()
    {
        $seriesForm = $this->createForm( SeriesType::class, new Series());

        return $this->renderForm('series/form.html.twig', compact('seriesForm'));
    }

    #[Route("/series/create", name: 'app_series_add',methods: ['POST'])]
    public function addSeries(Request $request)
    {    
        $series = new Series();
        $seriesForm = $this->createForm( SeriesType::class, $series);
        $seriesForm->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', compact('seriesForm'));
        }

        $this->addFlash('success', "Série {$series->getName()} adicionada com sucesso!");
        $this->seriesRepository->save($series, true);
        
        return new RedirectResponse('/series');
    }

    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]
    public function aditSeriesForm(Series $series): Response
    {
        $seriesForm = $this->createForm( SeriesType::class, $series, ['is_edit' => true]);
        return $this->renderForm('series/form.html.twig', compact('seriesForm', 'series'));
    }

    #[Route("/series/edit/{series}",  name: 'app_store_series_changes', methods: ['PATCH'])]
    public function storeSeriesChanges(Request $request, Series $series)
    {
        $seriesForm = $this->createForm( SeriesType::class, $series, ['is_edit' => true]);
        $seriesForm->handleRequest($request);
        
        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', compact('seriesForm', 'series'));
        }

        $this->addFlash('success', "Série editada com sucesso!");
        $this->entityManager->flush();

        return new RedirectResponse('/series');
    }

    #[Route(
        "/series/delete/{id}",
        name: 'app_series_delete', 
        methods: ['DELETE'],
        requirements: ['id' => '[0-9]+']
    )]
    public function deleteSeries(int $id, Request $request) 
    {
        $this->seriesRepository->removeById($id);
        $this->addFlash('success', 'Série removida com sucesso');

        return new RedirectResponse('/series');
    }
}
