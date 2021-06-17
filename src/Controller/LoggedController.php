<?php

namespace App\Controller;

use App\Entity\Form;

use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
#[Route('/api')]
class LoggedController extends AbstractController
{
    private $serializer;
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }


   /* #[Route('/addForm', name: 'addForm')]
    public function addForm():Response
    {
        $em = $this->getDoctrine()->getManager();
        $sr = SerializerBuilder::create()->build();
        $form = new Form();
        $em->persist($form);
        $em->flush();
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;


    }*/
    #[Route('/types', name: 'app_section_types')]
    public function getSectionTypes() :JsonResponse
    {
        $jsoncontent = $this->serializer->serialize($this->getDoctrine()->getRepository(Type::class)->findAll(), 'json');
        return JsonResponse::fromJsonString($jsoncontent);
    }

}
