<?php

namespace App\Controller;
use App\Entity\Article;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article")
     */

    public function index()
    {
    	$Article = $this->getDoctrine()
    	->getRepository('App:Article')
    	->findall();
        return $this->render('article/index.html.twig', [
            'Article' => $Article,
        ]);

    }

    /**
     * @Route("/article/new", name="new")
     */
    public function addArticle(Request $request)
    {

        $article = new Article();

        $form = $this->get('form.factory')->createBuilder(FormType::class, $article)
        ->add('titreArticle', TextType::class)
        ->add('contenuArticle', TextareaType::class)
        ->add('imageArticle', TextType::class)
        ->add('createdAt', DateType::class)
        ->add('valider', SubmitType::class)
        ->getForm()
        ;

        // Si la requête est en POST
        if($request->isMethod('POST')) {
        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $article dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('article', array('id' => $article->getId()));
            }
        }

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('article/new.html.twig', array(
          'form' => $form->createView(),
        ));
    }
   
}
