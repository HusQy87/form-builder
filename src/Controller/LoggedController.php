<?php

namespace App\Controller;

use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

#[Route('/api')]
class LoggedController extends AbstractController
{
    private $serializer;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->passwordEncoder = $passwordEncoder;
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
    public function getSectionTypes(): JsonResponse
    {
        $jsoncontent = $this->serializer->serialize($this->getDoctrine()->getRepository(Type::class)->findAll(), 'json');
        return JsonResponse::fromJsonString($jsoncontent);
    }

    #[Route('/users/me', name: 'app_get_user_username')]
    public function getUserByUsername()
    {
        $user = $this->getUser();
        if (!is_null($user))
        $user = [
            'username' => $user->getUsername(),
            'password' => '',
            'email' => $user->getEmail()
        ];
        return (new JsonResponse)->setData($user);

    }

    #[Route('/users', name: "api_update_user", methods: ['PUT'])]
    public function updateUSer(Request $request)
    {
        $isOk = true;
        $LoggedUser = $this->getUser();
        $response = new JsonResponse();
        $userInterface = ['username', 'email', 'oldPassword', 'newPassword', 'verifNewPassword'];
        foreach ($userInterface as $key) {
            $user[$key] = $request->get($key);
        }


        $validator = Validation::createValidator();
        $violations[] = $validator->validate($user['username'], [new Assert\Length(['min' => 3])]);
        $violations[] = $validator->validate($user['email'], [new Assert\Email()]);

        foreach ($violations as $violation) {
            if (count($violations) > 0) {
                $isOk = false;
            }
        }
        if ($user['newPassword'] !== '') {
            $violation = $validator->validate($user['newPassword'], [new Assert\Regex(['pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/'])]);
            if (count($violation) > 0) {
                $isOk = false;
            } else {
                if ($user['newPassword'] !== $user['verifNewPassword']) {
                    $isOk = false;

                } else {
                    if (!$this->passwordEncoder->isPasswordValid($LoggedUser, $user['oldPassword'])) {
                        $isOk = false;
                    } else {

                    }
                }
            }

        }

        if ($isOk = false) {
            $response->setStatusCode(401)
                ->setData(['code' => 401, 'message une erreur est survenue']);
            return $response;
        }
        if ($user['newPassword'] !== '') {
            $LoggedUser->setPassword($this->passwordEncoder->encodePassword($LoggedUser, $user['newPassword']));
        }
        $LoggedUser->setUsername($user['username']);
        $LoggedUser->setEmail($user['email']);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $response->setStatusCode(200)
            ->setData(['code' => 200, 'messages' => ["l'utilisateur a bien été modifié"]]);
        return $response;

    }

}
