<?php
namespace MauticPlugin\MauticVocativeBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Symfony\Component\Filesystem\Filesystem;

class MauticVocativeBundle extends PluginBundleBase
{
    public function boot()
    {
        require_once __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php';
    }

    /**
     * To provide dependent library without dependency on Composer.
     * For usage @see scripts section in composer.json
     */
    public static function includeLibraryToBundle()
    {
        $fs = new Filesystem();
        $to = __DIR__ . '/vendor/granam/czech-vocative/src';
        $fs->remove($to);
        $fs->mirror(
            $from = __DIR__ . '/../../vendor/granam/czech-vocative/src',
            $to = __DIR__ . '/vendor/granam/czech-vocative/src'
        );
        if (!is_dir(__DIR__ . '/vendor/granam/czech-vocative/src')) {
            throw new \RuntimeException("Unable to copy library from $from to $to");
        }
        $czechName = file_get_contents($czechNameSourceFile = __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php');
        if (empty($czechName)) {
            throw new \RuntimeException("Unable to read from $czechNameSourceFile");
        }
        $withLocalNamespace = preg_replace(
            '~namespace\s+CzechVocative~',
            'namespace MauticPlugin\MauticVocativeBundle\CzechVocative',
            $czechName
        );
        if (is_null($withLocalNamespace) || $czechName === $withLocalNamespace) {
            throw new \LogicException(
                'Unable to replace namespace from CzechVocative to MauticPlugin\MauticVocativeBundle\CzechVocative'
            );
        }
        if (!file_put_contents($czechNameTargetFile = __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php', $withLocalNamespace)) {
            throw new \RuntimeException("Unable to write into $czechNameTargetFile");
        }
    }
}
