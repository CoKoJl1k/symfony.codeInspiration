<?php

namespace App\Controller;

use App\Generator\FakeDataGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory as FakerFactory;
use App\Entity\Book;
use App\Entity\Author;

class FakeDataController extends AbstractController
{

    private $generator;

    public function __construct(FakeDataGenerator $generator)
    {
        $this->generator = $generator;
    }
    /**
     * @Route("/generate-fake-data", name="generate_fake_data")
     */
    public function generateFakeData(Request $request): Response
    {
        $this->generator->generateAuthors(10);
        $this->generator->generateBooks(20);

        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
}