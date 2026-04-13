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
            'address' => self::faker()->streetAddress(),

            'city' => self::faker()->city(),

            'country' => self::faker()->country(),

            'isBilling' => true,
            'isDelivery' => true,

            'name' => self::faker()->name(),

            'phone' => self::faker()->phoneNumber(),

            'postalCode' => self::faker()->postcode(),

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
