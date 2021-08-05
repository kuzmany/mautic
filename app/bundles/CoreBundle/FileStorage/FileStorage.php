<?php

/*
 * @copyright   2021 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\FileStorage;

use Mautic\CoreBundle\Helper\CoreParametersHelper;

class FileStorage
{
    const STORAGE_DEFAULT = 'file_storage';

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @var FileStorageInterface[]
     */
    private $storages = [];

    public function addStorage(string $id, FileStorageInterface $storage)
    {
        $this->storages[$id] = $storage;
    }

    public function getStorage(string $name = null)
    {
        if (!$name) {
            $name = $this->coreParametersHelper->get(self::STORAGE_DEFAULT);
        }
        if (isset($this->storages[$name])) {
            return $this->storages[$name];
        }

        throw new \InvalidArgumentException(sprintf('There is not a storage  %s', $name));
    }
}
