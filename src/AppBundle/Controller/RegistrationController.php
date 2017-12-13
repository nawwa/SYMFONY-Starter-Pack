<?php
/**
 * Created by PhpStorm.
 * User: nawwa
 * Date: 12/6/17
 * Time: 12:44 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Member;
use AppBundle\Forms\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController  extends Controller
{
    /**
     * @param $member
     * @return \Symfony\Component\Form\Form
     */
    private function createMemberRegistrationForm($member)
    {
        return $this->createForm(MemberType::class, $member, [
            'action' => $this->generateUrl('handle_registration_form_submission')
        ]);
    }

    /**
     * @Route("/register", name="registration")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        //Create a new instance of Member
        $member = new Member();

        //Set the form to be a MemberType Form
        $form = $this->createMemberRegistrationForm($member);

        return $this->render('registration/register.html.twig', [
            'registration_form' => $form->createView(),
        ]);
    }


    /**
     * @param Request $request
     * @Route("/registration-form-submission", name="handle_registration_form_submission")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function handleFormSubmissionAction(Request $request){
        //Create a new instance of Member
        $member = new Member();

        //Set the form to be a MemberType Form
        $form = $this->createMemberRegistrationForm($member);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('registration/register.html.twig', [
                'registration_form' => $form->createView(),
            ]);
        }

        //Encode password
        $password = $this
            ->get('security.password_encoder')
            ->encodePassword(
                $member,
                $member->getPlainPassword()
            );

        //Set the encoded password
        $member->setPassword($password);

        //Save the memeber into the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($member);
        $em->flush();

        //This token is what represent our user
        $token = new UsernamePasswordToken(
            $member,
            $password,
            'main',
            $member->getRoles()
        );

        //Save the token to the token storage
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        //Redirect to homepage
        return $this->redirectToRoute("homepage");
    }
}
