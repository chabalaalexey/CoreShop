<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Pimcore\PimcoreDriver;
use CoreShop\Component\Resource\Metadata\MetadataInterface;

final class DriverProvider
{
    /**
     * @var DriverInterface[]
     */
    private static $drivers = [];

    /**
     * @param MetadataInterface $metadata
     *
     * @return DriverInterface
     *
     * @throws UnknownDriverException
     */
    public static function get(MetadataInterface $metadata)
    {
        $type = $metadata->getDriver();

        if (isset(self::$drivers[$type])) {
            return self::$drivers[$type];
        }

        switch ($type) {
            case CoreShopResourceBundle::DRIVER_DOCTRINE_ORM:
                return self::$drivers[$type] = new DoctrineORMDriver();
            case CoreShopResourceBundle::DRIVER_DOCTRINE_PIMCORE:
                return self::$drivers[$type] = new PimcoreDriver();
        }

        throw new UnknownDriverException($type);
    }
}