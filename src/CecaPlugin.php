<?php

declare(strict_types=1);

namespace Sergiosanchezalvarez\CecaPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CecaPlugin
 * @package Sergiosanchezalvarez\CecaPlugin
 * @author Sergio Sánchez <sergiosanchezalvarez@gmail.com>
 */
final class CecaPlugin extends Bundle
{
    use SyliusPluginTrait;
}
