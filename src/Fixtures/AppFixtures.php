<?php


namespace App\Fixtures;


use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $data = [
                ['display_name' => 'Texte', 'value' => 'text'],
                ['display_name' => 'Titre', 'value' => 'title'],
            ];
        $users = [
            ['username' => 'thomas', 'email'=> 'thomasgalcera19@gmail.com', 'password' => 'password', 'ROLES' => ['ROLE_ADMIN'] ]
        ];

        foreach ($users as $user){
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->encoder->encodePassword($newUser, $user['password']));
            $newUser->setUsername($user['username']);
            $newUser->setRoles($user['ROLES']);
            $manager->persist($newUser);
        }

        foreach ($data as $datum){
            $type = new Type();
            $type->setDisplayName($datum['display_name'])
                ->setValue($datum['value']);
            $manager->persist($type);
        }
        $manager->flush();
    }
}