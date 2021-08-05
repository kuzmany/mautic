<?php

/*
 * @copyright   2021 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\FileStorage\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileDTO
{
    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var string|null
     */
    private $filename;

    public function __construct(UploadedFile $file, string $directory, string $filename = null)
    {
        $this->file      = $file;
        $this->directory = $directory;
        $this->filename  = $filename;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }
}
