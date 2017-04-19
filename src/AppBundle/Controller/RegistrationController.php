<?php
namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            // Setting profile picture
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $user->getProfileImage();

            $fileName = md5(uniqid()).'.'.$file->getExtension();

            $file->move(
                $this->getParameter('profileImage_directory'),
                $fileName
            );

            $user->setProfileImage($fileName);

            // Encrypting plain password
            $password = $this->get('security.password_encoder')

                ->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);


            $em = $this->getDoctrine()->getManager();

            $em->persist($user);

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}