<?php

namespace App\Generator;

use Faker\Factory;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;


class FakeDataGenerator
{
    private $entityManager;
    private $faker;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Factory::create();
    }

    public function generateAuthors(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $author = new Author();
            $author->setFirstName($this->faker->firstName);
            $author->setLastName($this->faker->lastName);
            $author->setNumberOfBooks($this->faker->numberBetween(1,10));
            $this->entityManager->persist($author);
        }
        $this->entityManager->flush();
    }

    public function generateBooks(int $count)
    {
        $authors = $this->entityManager->getRepository(Author::class)->findAll();
        for ($i = 0; $i < $count; $i++) {
            $book = new Book();
            $book->setName($this->faker->sentence(2));
            $book->setDescription($this->faker->sentence);
            $book->setDateCreated($this->faker->dateTime);
            $book->addAuthor($this->faker->randomElement($authors));
            $this->entityManager->persist($book);
        }
        $this->entityManager->flush();
    }
}