<?php
namespace App\Controller;

use App\Entity\Book;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request): Response
    {
        $search = trim($request->query->get('search'));
        $books = $this->getDoctrine()->getRepository(Book::class)->findByFilters($search);
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
}