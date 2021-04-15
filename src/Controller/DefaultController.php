<?php

namespace App\Controller;

use App\Entity\Form;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    #[Route('/addForm', name: 'addForm')]
    public function addForm():Response
    {
        $em = $this->getDoctrine()->getManager();
        $sr = SerializerBuilder::create()->build();
        $form = new Form();
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $em->persist($form);
        $em->flush();
        $data = $sr->serialize($form, 'json', $context);
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;


    }
}
