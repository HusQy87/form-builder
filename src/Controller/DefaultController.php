<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class DefaultController extends AbstractController
{
    private $serializer;
    private $logger;
    private $encoder;

    public function __construct(LoggerInterface $logger, UserPasswordEncoderInterface $encoder)
    {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/', name: 'default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
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

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login_check(Request $request): JsonResponse
    {
        $this->logger->info('Login tried');
        $user = $this->getUser();
        if ($user){
            return $this->json(['username' => $user->getUsername(), 'roles' => $user->getRoles()]);
        }

    }

    #[Route('/users', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, VerifyEmailHelperInterface $emailHelper, MailerInterface $mailer)
    {

        $response = new JsonResponse();
        $this->logger->info('register new user tried');
        $user = [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
        foreach ($user as $key => $value) {
            if ($value === null) {
                $response = new JsonResponse();
                $response->setStatusCode(401);
                $this->logger->error('Registration failed', [$key => null]);
                return new JsonResponse();
            }
        }
        $validator = Validation::createValidator();
        $violations[] = $validator->validate($user['username'], [
            new Assert\Length(['min' => 3, 'minMessage' => "Le nom d'utilisateur doit comporter au moins 3 caractères "]),
        ]);
        $violations[] = $validator->validate($user['email'], [new Assert\Email(['message' => "cette email n'est pas valide"])]);
        $violations[] = $validator->validate(
            $user['password'],
            [
                new Assert\Regex([
                    'pattern' => "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/",
                    'message' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre, un caractère spécial et doit contenir au moins 8 caractères'
                ])
            ]);

        $readyToAdd = true;
        $messages = [];
        foreach ($violations as $violation)
        {
            if ($violation->count()){
                $readyToAdd =false;
                $messages[] = $violation->get(0)->getMessage();
                $this->logger->error("création de l'utilisateur {$user['username']} impossible", ['error' =>$violation->get(0)->getMessage() ] );
            }
        }
        if ($readyToAdd){
            try {
                $userToAdd = new User();
                $userToAdd->setEmail($user['email'])
                    ->setUsername($user['username'])
                    ->setPassword($this->encoder->encodePassword($userToAdd, $user['password']));
                $em =  $this->getDoctrine()->getManager();
                $em->persist($userToAdd);
                $em->flush();

                $signatureComponents = $emailHelper->generateSignature('registration_confirmation_route', $userToAdd->getId(), $userToAdd->getEmail());

                $email = new TemplatedEmail();
                $email->to($userToAdd->getEmail());
                $email->from("tamerelapute@gmail.com");
                $email->htmlTemplate('mails/comfirmation_email.html.twig');
                $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
                $mailer->send($email);

                $this->getDoctrine()->getManager()->flush();
            }catch (UniqueConstraintViolationException $exception){

                $response->setData(['code' => 401, 'messages' => ["Ce nom d'utilisateur ou cette email est déja utilisé"]]);
                $response->setStatusCode(401);
                return $response;
            }

        }else
        {
            $response->setStatusCode(401)
                ->setData(['code'=> 401, 'messages' => $messages]);
        }

    }

    #[Route('/verify', name: "registration_confirmation_route")]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $emailHelper): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Do not get the User's Id or Email Address from the Request object
        try {
            $emailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute("api_login");
        }

        // Mark your user as verified. e.g. switch a User::verified property to true

        $this->addFlash('success', 'Your e-mail address has been verified.');


    }
}
