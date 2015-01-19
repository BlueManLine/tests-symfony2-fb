<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rss;
use AppBundle\Form\RssType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $rss = new Rss();
        $rss->setUser($this->getUser());

        $form = $this->createForm(new RssType(), $rss);

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $formData = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($rss);
            $em->flush();
        }

        return $this->render(
            'default/index.html.twig',
            [
                'form' => $form->createView(),
                'formData' => isset($formData) ? $formData : NULL
            ]
        );
    }

}
