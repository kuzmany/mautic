<?php

/*
 * @copyright   2021 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\FileStorage\Local;

use Mautic\CoreBundle\FileStorage\DTO\UploadFileDTO;
use Mautic\CoreBundle\FileStorage\FileStorageInterface;

class LocalFileStorage implements FileStorageInterface
{
    public function moveFromFileData(UploadFileDTO $uploadFileDTO)
    {
        $uploadFileDTO->getFile()->move($uploadFileDTO->getDirectory(), $uploadFileDTO->getFilename());
    }
}
