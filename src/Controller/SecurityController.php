<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Security\LoginFormAuthenticator;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * Registration page for new users.
     */
    #[Route('/inscription', name: 'security_registration')]
    public function registration(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        // Create a new user and registration form
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Handle password hashing - récupérer depuis le formulaire car plainPassword n'est pas mappé
                $plainPassword = $form->get('plainPassword')->get('first')->getData();
                if (!$plainPassword) {
                    throw new \InvalidArgumentException('Password cannot be empty');
                }

                // Hash the password and assign default ROLE_USER
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $user->setRoles(['ROLE_USER']);

                // Débogage avant la persistance de l'utilisateur
                dump([
                    'Formulaire soumis' => $form->getData(),
                    'Champs du formulaire' => [
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'plainPassword' => $plainPassword, // Utiliser la variable récupérée du formulaire
                        'roles' => $user->getRoles(),
                    ],
                    'Formulaire valide' => $form->isValid(),
                    'Erreurs du formulaire' => $form->getErrors(true, true),
                ]);

                // Save the user
                $entityManager->persist($user);
                $entityManager->flush();

                // Success message
                $this->addFlash('success', 'Votre compte a été créé avec succès !');

                // Connecter automatiquement l'utilisateur après l'inscription
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte: '.$e->getMessage());
            }
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Login page.
     */
    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already logged in, redirect to home page
        if ($this->getUser()) {
            $this->addFlash('info', 'Vous êtes déjà connecté.');

            return $this->redirectToRoute('blog_index');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash('error', 'Identifiants invalides.');
        }

        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/sign.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Logout action
     * This method can be blank - it will be intercepted by the logout key on the firewall
     * The actual logout logic is handled by Symfony security system.
     */
    #[Route('/deconnexion', name: 'security_logout')]
    public function logout(): void
    {
        // This method will never be executed as Symfony handles the logout
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
