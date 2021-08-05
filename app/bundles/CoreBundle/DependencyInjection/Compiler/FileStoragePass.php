<?php

/*
 * @copyright   2021 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileStoragePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mautic.file.storage')) {
            return;
        }

        $storageDef = $container->findDefinition('mautic.file.storage');

        foreach ($container->findTaggedServiceIds('mautic.file.storage.service') as $id => $tags) {
            $storageDef->addMethodCall('addStorage', [$id, new Reference($id)]);
        }
    }
}
