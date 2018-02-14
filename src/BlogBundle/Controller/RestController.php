<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Article;
use BlogBundle\Entity\ArticleCategory;
use BlogBundle\Entity\Contact;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Annotations;

class RestController extends FOSRestController {
    public function getCategoriesAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository(ArticleCategory::class);
        $data = $repo->findAll();
        $view = $this->view($data, 200);
        $context = $view->getContext();

        $context->addGroups(['Default']);
        $view->setContext($context);

        return $this->handleView($view);
    }

    public function getArticlesAction(Request $request) {
        $currentPageNr = $request->query->getInt('page', 1);
        $currentPageLimit = $request->query->getInt('limit', ArticleController::PAGINATION_LIMITS[1]);

        $repo = $this->getDoctrine()->getManager()->getRepository(Article::class);
        $data = $repo->getPaginated($currentPageNr, $currentPageLimit);
        $view = $this->view($data, 200);
        $context = $view->getContext();

        $context->addGroups(['Default']);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getArticleAction(Article $article) {
        $view = $this->view($article, 200);
        $context = $view->getContext();

        $context->addGroups(['Default', 'detail']);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     * @Annotations\RequestParam(name="firstName", nullable=true, strict=true, description="First name")
     * @Annotations\RequestParam(name="lastName", nullable=true, strict=true, description="Last name")
     * @Annotations\RequestParam(name="email", nullable=false, allowBlank=false, strict=true, description="Email")
     * @Annotations\RequestParam(name="content", nullable=false, allowBlank=false, strict=true, description="Content")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postContactsAction(ParamFetcher $paramFetcher)
    {
        $contact = new Contact();

        $contact
            ->setFirstName($paramFetcher->get('firstName'))
            ->setLastName($paramFetcher->get('lastName'))
            ->setEmail($paramFetcher->get('email'))
            ->setContent($paramFetcher->get('content'));

        $validator = $this->get('validator');
        $errors = $validator->validate($contact);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $view = $this->view($contact, 200);
            return $this->handleView($view);
        }

        $view = $this->view($errors, 400);
        return $this->handleView($view);
    }
}
