<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;
class IndexController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    #[Route('/', name: 'article_list')]
public function home(Request $request, EntityManagerInterface $entityManager): Response
{
    $propertySearch = new PropertySearch();
    $searchForm = $this->createForm(PropertySearchType::class, $propertySearch);
    $searchForm->handleRequest($request);
    
    // Initialement le tableau des articles est vide
    $articles = [];
    
    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
        // On récupère le nom d'article tapé dans le formulaire
        $nom = $propertySearch->getNom();
        if ($nom != "") {
            // Si on a fourni un nom d'article, on affiche tous les articles ayant ce nom
            $articles = $entityManager->getRepository(Article::class)->findBy(['nom' => $nom]);
        } else {
            // Si aucun nom n'est fourni, on affiche tous les articles
            $articles = $entityManager->getRepository(Article::class)->findAll();
        }
    } else {
        // Par défaut, afficher tous les articles
        $articles = $entityManager->getRepository(Article::class)->findAll();
    }
    
    return $this->render('articles/index.html.twig', [
        'articles' => $articles,
        'searchForm' => $searchForm->createView()
    ]);
}

    #[Route('/article/save', name: 'save_article')]
    public function save(EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix('1000.00');
        
        $entityManager->persist($article);
        $entityManager->flush();
        
        return new Response('Article enregistré avec id '.$article->getId());
    }

#[Route('/article/new', name: 'new_article')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $article = new Article();
    
    $form = $this->createForm(ArticleType::class, $article);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($article);
        $entityManager->flush();
        
        $this->addFlash('success', 'Article créé avec succès!');
        return $this->redirectToRoute('article_list');
    }
    
    return $this->render('articles/new.html.twig', [
        'form' => $form->createView()
    ]);
}

#[Route('/article/edit/{id}', name: 'edit_article')]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $article = $entityManager->getRepository(Article::class)->find($id);
    
    if (!$article) {
        throw $this->createNotFoundException('Article non trouvé');
    }
    
    $form = $this->createForm(ArticleType::class, $article);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        
        $this->addFlash('success', 'Article modifié avec succès!');
        return $this->redirectToRoute('article_list');
    }
    
    return $this->render('articles/edit.html.twig', [
        'form' => $form->createView(),
        'article' => $article
    ]);
}

    #[Route('/article/{id}', name: 'article_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }
    #[Route('/category/new', name: 'new_category')]
public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
{
    $category = new Category();
    
    $form = $this->createForm(CategoryType::class, $category);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($category);
        $entityManager->flush();
        
        $this->addFlash('success', 'Catégorie créée avec succès!');
        return $this->redirectToRoute('article_list');
    }
    
    return $this->render('articles/newCategory.html.twig', [
        'form' => $form->createView()
    ]);
}

 

    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        $entityManager->remove($article);
        $entityManager->flush();
        
        $this->addFlash('success', 'Article supprimé avec succès!');
        return $this->redirectToRoute('article_list');
    }


    #[Route('/art_cat/', name: 'article_par_cat')]
public function articlesParCategorie(Request $request, EntityManagerInterface $entityManager): Response
{
    $categorySearch = new CategorySearch();
    $form = $this->createForm(CategorySearchType::class, $categorySearch);
    $form->handleRequest($request);

    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $category = $categorySearch->getCategory();

        if ($category != null) {
            $articles = $category->getArticles();
        } else {
            $articles = $entityManager->getRepository(Article::class)->findAll();
        }
    } else {
        $articles = $entityManager->getRepository(Article::class)->findAll();
    }

    return $this->render('articles/articlesParCategorie.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles
    ]);
}

#[Route('/art_prix/', name: 'article_par_prix')]
public function articlesParPrix(Request $request, EntityManagerInterface $entityManager): Response
{
    $priceSearch = new PriceSearch();
    $form = $this->createForm(PriceSearchType::class, $priceSearch);
    $form->handleRequest($request);

    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $minPrice = $priceSearch->getMinPrice();
        $maxPrice = $priceSearch->getMaxPrice();

        if ($minPrice !== null && $maxPrice !== null) {
            $articles = $entityManager->getRepository(Article::class)->findByPriceRange($minPrice, $maxPrice);
        } else {
            $articles = $entityManager->getRepository(Article::class)->findAll();
        }
    } else {
        $articles = $entityManager->getRepository(Article::class)->findAll();
    }

    return $this->render('articles/articlesParPrix.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles
    ]);
}
}