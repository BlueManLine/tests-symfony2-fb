<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rss;
use AppBundle\Entity\RssComment;
use AppBundle\Form\RssCommentType;
use AppBundle\Form\RssType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Configuration\Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $rss = new Rss();
        $rss->setUser($this->getUser());

        $form = $this->createForm(new RssType(), $rss);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($rss);
                $em->flush();

                $this->addFlash('success', 'Record created');

                return $this->redirectToRoute('dashboard');
            }
        }

        $em = $this->getDoctrine()->getManager();
        $rsses = $em->getRepository('AppBundle:Rss')->findBy(['user'=>$this->getUser()], ['created_at' => 'DESC']);

        $rssComment = new RssComment();
        $formComment = $this->createForm(new RssCommentType(), $rssComment);

        return $this->render('Default/index.html.twig', [
            'form' => $form->createView(),
            'formData' => isset($formData) ? $formData : NULL,
            'formComment' => $formComment->createView(),
            'rsses' => $rsses,
        ]);
    }

    /**
     * @Configuration\Route("/comment/{id}", name="comment_save")
     * @Configuration\Method("POST")
     *
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function commentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Rss $rssRepo */
        $rssRepo = $em->getRepository('AppBundle:Rss')->findOneById($id);

        $rssComment = new RssComment();
        $rssComment->setUser($this->getUser());
        $rssComment->setRss($rssRepo);

        $formComment = $this->createForm(new RssCommentType(), $rssComment);
        $formComment->handleRequest($request);

        if ($formComment->isValid()) {
            $em->persist($rssComment);
            $em->flush();

            $this->addFlash('success', 'Comment created');
        }

        return $this->redirectToRoute('dashboard');
    }

}
