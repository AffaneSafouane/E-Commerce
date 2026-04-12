<?php

namespace App\Factory;

use App\Entity\CustomerAddress;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<CustomerAddress>
 */
final class CustomerAddressFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return CustomerAddress::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'address' => self::faker()->text(255),
            'city' => self::faker()->text(255),
            'country' => self::faker()->text(255),
            'isBilling' => self::faker()->boolean(),
            'isDelivery' => self::faker()->boolean(),
            'name' => self::faker()->text(255),
            'phone' => self::faker()->text(15),
            'postalCode' => self::faker()->text(255),
            'userAccount' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(CustomerAddress $customerAddress): void {})
        ;
    }
}
