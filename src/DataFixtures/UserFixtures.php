<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@symfony.com')
            ->setUsername('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setProfilePicture('https://randomuser.me/api/portraits/men/1.jpg');
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'password'
        );
        $admin->setPassword($hashedPassword);
        
        $manager->persist($admin);
        $this->addReference('admin-user', $admin);
        
        // Create test user
        $testUser = new User();
        $testUser->setEmail('test@example.com')
            ->setUsername('test')
            ->setRoles(['ROLE_USER'])
            ->setProfilePicture('https://randomuser.me/api/portraits/men/22.jpg');
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $testUser,
            'password'
        );
        $testUser->setPassword($hashedPassword);
        
        $manager->persist($testUser);
        $this->addReference('test-user', $testUser);
        
        // Create regular users
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setRoles(['ROLE_USER']);
                
            // Alternate between men and women profile pictures
            if ($i % 2 === 0) {
                $user->setProfilePicture("https://randomuser.me/api/portraits/men/{$i}.jpg");
            } else {
                $user->setProfilePicture("https://randomuser.me/api/portraits/women/{$i}.jpg");
            }
            
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($hashedPassword);
            
            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }
        
        $manager->flush();
    }
}

