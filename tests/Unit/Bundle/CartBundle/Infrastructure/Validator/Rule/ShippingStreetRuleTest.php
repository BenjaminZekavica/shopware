<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Bundle\CartBundle\Infrastructure\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Shopware\Cart\Cart\CalculatedCart;
use Shopware\Cart\Delivery\ShippingLocation;
use Shopware\CartBridge\Rule\ShippingStreetRule;
use Shopware\Address\Struct\Address;
use Shopware\Framework\Struct\IndexedCollection;
use Shopware\Context\Struct\ShopContext;
use Shopware\Country\Struct\Country;
use Shopware\CountryState\Struct\CountryState;

class ShippingStreetRuleTest extends TestCase
{
    public function testWithExactMatch(): void
    {
        $rule = new ShippingStreetRule('example street');

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getShippingLocation')
            ->will($this->returnValue(
                ShippingLocation::createFromAddress(
                    $this->createAddress('example street')
                )
            ));

        $this->assertTrue(
            $rule->match($cart, $context, new IndexedCollection())->matches()
        );
    }

    public function testCaseInsensitive(): void
    {
        $rule = new ShippingStreetRule('ExaMple StreEt');

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getShippingLocation')
            ->will($this->returnValue(
                ShippingLocation::createFromAddress(
                    $this->createAddress('example street')
                )
            ));

        $this->assertTrue(
            $rule->match($cart, $context, new IndexedCollection())->matches()
        );
    }

    public function testNotMatch(): void
    {
        $rule = new ShippingStreetRule('example street');

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getShippingLocation')
            ->will($this->returnValue(
                ShippingLocation::createFromAddress(
                    $this->createAddress('test street')
                )
            ));

        $this->assertFalse(
            $rule->match($cart, $context, new IndexedCollection())->matches()
        );
    }

    public function testWithoutAddress(): void
    {
        $rule = new ShippingStreetRule('ExaMple StreEt');

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getShippingLocation')
            ->will($this->returnValue(
                ShippingLocation::createFromCountry(
                    new Country()
                )
            ));

        $this->assertFalse(
            $rule->match($cart, $context, new IndexedCollection())->matches()
        );
    }

    private function createAddress(string $street): Address
    {
        $address = new Address();
        $state = new CountryState();
        $country = new Country();
        $state->setCountry($country);

        $address->setStreet($street);
        $address->setCountry($country);
        $address->setState($state);

        return $address;
    }
}
