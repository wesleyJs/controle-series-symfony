<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    public function __construct(
        private SeriesRepository $seriesRepository        
    ) {  
    }

    #[Route('/series', name: 'series', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();
        
         return $this->render('series/index.html.twig', [
            'series' => $seriesList
        ]);
    }

    #[Route('/series/create', name: 'serie_create',methods: ['GET'])]
    public function addSeriesForm()
    {
        return $this->render('series/form.html.twig');
    }

    #[Route("/series/create", methods: ['POST'])]
    public function addSeries(Request $request)
    {
        $seriesName = $request->get('name');
        $series = new Series($seriesName);
        
        $this->seriesRepository->save ($series, true);
        return new RedirectResponse('/series');
    }
}
