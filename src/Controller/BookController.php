<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    private $entityManager;
    private $authorRepository;
    private $bookRepository;
    private $fileService;

    public function __construct(EntityManagerInterface $entityManager, AuthorRepository $authorRepository,BookRepository $bookRepository,FileService $fileService)
    {
        $this->entityManager = $entityManager;
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
        $this->fileService = $fileService;
    }

    /**
     * @Route("/", name="app_book_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $filters = $request->query->all();
        $books = $this->getDoctrine()->getRepository(Book::class)->findByFilters($filters);
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/new", name="app_book_new", methods={"GET", "POST"})
     * @param Request $request
     * @param BookRepository $bookRepository
     * @param $entityManager
     * @return Response
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $file = $form['image']->getData();
            if (!empty($file)) {
                $path = $this->getParameter('kernel.project_dir') . "/public/uploads/books";
                $book = $this->fileService->saveFile($book, $file, $path);
            }
            $book->setName($data['book']['name']);
            $book->setDescription($data['book']['description']);

            foreach ($data['book']['authors'] as $authorId) {
                $author = $this->authorRepository->find($authorId);
                $book->addAuthor($author);
            }

            $this->bookRepository->add($book);

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_book_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if (!empty($file)) {
                $path = $this->getParameter('kernel.project_dir') . "/public/uploads/books";
                $book = $this->fileService->saveFile($book, $file, $path);
            }
            $bookRepository->add($book);
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $bookRepository->remove($book);
        }
        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
