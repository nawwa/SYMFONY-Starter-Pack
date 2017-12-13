<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms\ContactForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SupportController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ContactForm::class, null, [
        'action' => $this->generateUrl('handle_form_submission'),
        ]);

        // replace this example code with whatever you need
        return $this->render('support/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'our_form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @Route("/form-submission", name="handle_form_submission")
     * @Method("POST")
     */
    public function handleFormSubmissionAction(Request $request)
    {
        $form = $this->createForm(ContactForm::class);
        $form->handleRequest($request);

        if ( ! $form->isSubmitted() || ! $form->isValid())
        {
            return $this->redirectToRoute('homepage');
        }

        $data = $form->getData();

        dump($data);

        return $this->redirectToRoute('homepage');
    }
}
