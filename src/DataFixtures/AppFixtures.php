<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Trajet;
use App\Entity\Animal;

class AppFixtures extends Fixture
{
	private $userPasswordHasher;
	
	public function __construct(UserPasswordHasherInterface $userPasswordHasher)
	{
		$this->userPasswordHasher = $userPasswordHasher;
	}
	
	public function load(ObjectManager $manager): void
	{
		$user = new User();
		$user->setEmail("user@bookapi.com")
			->setNom("Gourdon")
			->setPrenom("Joris")
			->setTelephone("0789656787")
			->setRoles(["ROLE_USER"])
			->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
		$manager->persist($user);
		
		$userAdmin = new User();
		$userAdmin->setEmail("admin@bookapi.com")
			->setNom("Auneau")
			->setPrenom("Jérémie")
			->setTelephone("0684756787")
			->setRoles(["ROLE_ADMIN"])
			->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password_admin"));
		$manager->persist($userAdmin);

		$trajet = new Trajet();
		$trajet
			->setLieuDepart('13 rue des jardins, Angers, 49000')
			->setLieuDestination('26 rue des jardins, Angers, 49000')
			->setDateHeureDepart(new \DateTime())
			->setPlacesDisponible(3)
			->setPrixParPersonne(20)
			->setDescription('Description du trajet')
			->setUser($user);

		$animal1 = new Animal();
		$animal1->setPrenom('Fido')
			->setDescription('Chien adorable')
			->setAge(3);
		$animal1->setUser($user);
		$manager->persist($animal1);

		$animal2 = new Animal();
		$animal2->setPrenom('Whiskers')
			->setDescription('Chat curieux')
			->setAge(2);
		$animal2->setUser($userAdmin);
		$manager->persist($animal2);
		
		$manager->persist($trajet);
		$manager->flush();
	}
}
