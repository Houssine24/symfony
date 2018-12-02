<?php

namespace App\Controller;
use App\Entity\Article;
use App\Entity\Commentaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index()
    {
        $Commentaire = $this->getDoctrine()
        ->getRepository('App:Commentaire')
        ->findall();
        
        $Article = $this->getDoctrine()
        ->getRepository('App:Article')
        ->findall();
        return $this->render('commentaire/index.html.twig', [
            'Commentaire' => $Commentaire,
            'Article' => $Article,
        ]);
    }
    /**
     * @Route("/commentaire/newCom", name="newCom")
     */

    public function addCommentaire(Request $request)
    {
        $article = $_GET['article_id'];

        $art = $this->getDoctrine()
        ->getRepository('App:Article')
        ->findOneBy([
            "id" => $article
        ]);

        $comment = new Commentaire();
        $form = $this->get('form.factory')->createBuilder(FormType::class, $comment)
        ->add('contenuCommentaire', TextareaType::class)
        ->add('createdAt', DateType::class)
        ->add('createdAt', DateType::class)
        ->add('article', EntityType::class, array(
            'class' => 'App:Article',
            'data' => $art,
        ))
        ->add('valider', SubmitType::class)
        ->getForm();

        // Si la requête est en POST
        if($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $comment contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);
            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $article dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Commentaire bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('commentaire', array('id' => $comment->getId()));
            }
        }

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('commentaire/newCom.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/commentaire/newSousCom", name="newSousCom")
     */

    public function addSousCommentaire(Request $request)
    {
        $commentaire = $_GET['commentaire_id'];

        $comm = $this->getDoctrine()
        ->getRepository('App:Commentaire')
        ->findOneBy([
            "id" => $commentaire
        ]);

        $sousCom = new Commentaire();
        $form = $this->get('form.factory')->createBuilder(FormType::class, $sousCom)
        ->add('contenuCommentaire', TextareaType::class)
        ->add('createdAt', DateType::class)
        ->add('createdAt', DateType::class)
        ->add('fkSousCommentaire', EntityType::class, array(
            'class' => 'App:Commentaire',
            'data' => $comm,
        ))
        ->add('valider', SubmitType::class)
        ->getForm();

        // Si la requête est en POST
        if($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $sousCom contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);
            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $article dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($sousCom);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Sous Commentaire bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('commentaire', array('id' => $sousCom->getId()));
            }
        }

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('commentaire/newSousCom.html.twig', array(
          'form' => $form->createView(),
        ));
    }
   
    
}
