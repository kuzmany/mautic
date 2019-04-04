<?php
/**
 *  * @copyright   2019 Mautic Contributors. All rights reserved
 *  * @author      Mautic
 *  *
 *
 *  * @see        http://mautic.org
 *  *
 *
 *  * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

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

namespace Mautic\CoreBundle\Uploader\Decorator;

use Mautic\CoreBundle\Entity\CommonEntity;
use Mautic\CoreBundle\Exception\FileNotFoundException;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\PathsHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractUploaderDecorator
{
    const SYSTEM_PATH = 'assets';

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var PathsHelper
     */
    private $pathsHelper;

    /**
     * @var CommonEntity
     */
    private $entity;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * UploadDecoratorPath constructor.
     *
     * @param CoreParametersHelper $coreParametersHelper
     */
    public function __construct(CoreParametersHelper $coreParametersHelper, PathsHelper $pathsHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->pathsHelper          = $pathsHelper;
        $this->propertyAccessor     = new PropertyAccessor();
    }

    /**
     * @param bool $fullPath
     *
     * @return string
     */
    public function getUploadPathDirectory()
    {
        return $this->pathsHelper->getSystemPath(get_called_class()::SYSTEM_PATH, true).DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $this->getUploadDirectory());
    }

    /**
     * @return string
     */
    public function getUploadUrlDirectory()
    {
        return  $this->coreParametersHelper->getParameter('site_url').'/'.$this->pathsHelper->getSystemPath(get_called_class()::SYSTEM_PATH).'/'.implode('/', $this->getUploadDirectory());
    }

    /**
     * @param $field
     *
     * @return string
     */
    public function getFileLink($field)
    {
        return $this->getUploadUrlDirectory().'/'.$this->propertyAccessor->getValue($this->entity, $field);
    }

    /**
     * @param $field
     *
     * @return string
     */
    public function getFilePath($field)
    {
        return $this->getUploadPathDirectory().DIRECTORY_SEPARATOR.$this->getFileName($field);
    }

    /**
     * @param $field
     *
     * @return string
     */
    private function getFileName($field)
    {
        return $this->propertyAccessor->getValue($this->entity, $field);
    }

    /**
     * @param string $field
     * @param int    $decimals
     *
     * @return string
     */
    public function getFileSize($field, $decimals  = 2)
    {
        $fileDecorator = new FileDecorator($this->getFilePath($field));

        return $fileDecorator->getFileSize($decimals);
    }

    /**
     * @param $field
     *
     * @return bool|Response
     */
    public function downloadFile($field)
    {
        $fileDecorator = new FileDecorator($this->getFilePath($field));
        $entity        = $this->getEntity();
        if (!empty($entity)) {
            $response = new Response();
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
            $response->headers->set('Content-Type', $fileDecorator->getFileMimeType());
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$this->getFileName($field));
            $response->setContent(file_get_contents($this->getFilePath($field)));

            return $response;
        }

        throw new FileNotFoundException();
    }

    /**
     * @return array
     */
    public function getUploadDirectory()
    {
        return [get_called_class()::UPLOAD_DIRECTORY];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return get_called_class()::FIELDS;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return get_called_class()::FORM;
    }

    /**
     * @return CommonEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param CommonEntity $entity
     */
    public function setEntity(&$entity)
    {
        $this->entity = $entity;
    }
}
