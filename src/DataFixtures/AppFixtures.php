<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\CustomerAddressFactory;
use App\Factory\MediaFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'admin@lookup.fr',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'admin123',
            'firstName' => 'Admin',
            'name' => 'LookUp'
        ]);

        UserFactory::createOne([
            'email' => 'user@lookup.fr',
            'roles' => ['ROLE_USER'],
            'password' => 'user123',
            'firstName' => 'Astrid',
            'name' => 'Nova'
        ]);

        CategoryFactory::createMany(5, function () {
            return [
                'products' => ProductFactory::new()->many(4),
                'media' => MediaFactory::new()->many(1),
            ];
        });

        UserFactory::createMany(10, function () {
            return [
                'customerAddresses' => CustomerAddressFactory::new()->many(1),
            ];
        });
    }
}