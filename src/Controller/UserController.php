<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * Formulaire d'inscription d'un User
     * @Route("/membre/inscription", name="user_create", methods={"GET|POST"})
     * @param Request $request
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        #1. Création d'un objet user
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        #2. Création du formulaire
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            #3. Encodage du MDP
            $user->setPAssword(
                $encoder->encodePassword($user, $user->getPassword())
            );

            #4. Sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            #5. Notification FLash
            $this->addFlash('notice', 'Félicitation pour votre inscription !');

            #6. Redirection FIXME modifier l'url vers page connexion
            return $this->redirectToRoute('index');

        } #endif


        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

}