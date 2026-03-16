<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\DataFixtures\UserFixtures;
use App\Entity\User;

class LivreFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void  
    {
        $faker = Factory::create('fr_FR');  

        // Création des catégories de livres
        $categories = ['Science-Fiction', 'Biographie', 'Histoire', 'Fantasy', 'Thriller', 'Roman'];
        foreach ($categories as $c) {
            $category = (new Category())
                ->setName($c)
                ->setSlug($this->slugger->slug($c))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            $this->addReference($c, $category);
        }

         // Création des livres
        for ($i = 1; $i <= 20; $i++) {
            $title = $faker->sentence(3);
            $author = $faker->name;
            $livre = (new Livre())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setAuthor($author)
                ->setPublicationYear($faker->year)
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setSummary($faker->paragraphs(3, true))
                ->setPublisher($faker->company)
                ->setLanguage($faker->languageCode)
                ->setGenre($faker->randomElement(['Fiction', 'Non-Fiction', 'Mystery', 'Adventure']))
                ->setEdition($faker->randomElement(['1st', '2nd', 'Revised']))
                ->setCoverImage($faker->imageUrl(200, 300, 'books'))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class)); // Liaison à une catégorie

            // Associer les 5 premiers livres à l'admin, les autres à un utilisateur lambda
            if ($i <= 5) {
                $livre->setUser($this->getReference(UserFixtures::ADMIN, User::class)); // Liaison à un utilisateur admin
            } else {
                $livre->setUser($this->getReference('USER' . $faker->numberBetween(1, 10), User::class)); // Liaison à un utilisateur fictif
            }

            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
