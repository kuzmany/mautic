<?php
/*
 *  * @copyright   2019 Mautic Contributors. All rights reserved
 *  * @author      Mautic
 *  *
 *
 *  * @see        http://mautic.org
 *  *
 *
 *  * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Uploader\Locator;

use Mautic\CoreBundle\Uploader\AbstractUploader;

class FileLocator
{
    /**
     * @var AbstractUploader
     */
    private $uploaderFactory;

    /**
     * @var DirectoryLocator
     */
    private $directoryLocator;

    /**
     * FileLocator constructor.
     *
     * @param AbstractUploader $uploaderFactory
     * @param string           $fileName
     */
    public function __construct(AbstractUploader $uploaderFactory)
    {
        $this->uploaderFactory  = $uploaderFactory;
        $this->directoryLocator = new DirectoryLocator($this->uploaderFactory);
    }

    /**
     * @return string
     */
    public function getFileLink($fileName)
    {
        return $this->directoryLocator->getUploadUrlDirectory().'/'.$fileName;
    }

    /**
     * @return string
     */
    public function getFilePath($fileName)
    {
        return $this->directoryLocator->getUploadPathDirectory().DIRECTORY_SEPARATOR.$fileName;
    }
}
