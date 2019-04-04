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
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Uploader;

use Mautic\CoreBundle\Exception\FileUploadException;
use Mautic\CoreBundle\Helper\FileUploader;
use Mautic\CoreBundle\Uploader\Decorator\AbstractUploaderDecorator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Uploader
{
    /** @var FileUploader */
    private $fileUploader;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    /** @var null|\Symfony\Component\HttpFoundation\Request */
    private $request;

    /**
     * LeadNotesUploader constructor.
     *
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader, RequestStack $requestStack)
    {
        $this->fileUploader         = $fileUploader;
        $this->propertyAccessor     = new PropertyAccessor();
        $this->request              = $requestStack->getCurrentRequest();
    }

    /**
     * @param AbstractUploaderDecorator $decorator
     */
    public function uploadFiles(AbstractUploaderDecorator $decorator)
    {
        $files = [];
        if (isset($this->request->files->all()[$decorator->getForm()])) {
            $files = $this->request->files->all()[$decorator->getForm()];
        }

        $entityChange = false;

        foreach ($decorator->getFields() as $field) {
            // nothing for upload
            if (empty($files[$field])) {
                // Delete
                if (!empty($this->request->request->all()[$decorator->getForm()][$field.'_remove'])) {
                    $this->fileUploader->delete($decorator->getFilePath($field));
                    if ($entity = $decorator->getEntity()) {
                        $this->propertyAccessor->setValue($entity, $field, '');
                    }
                    $entityChange = true;
                }
                continue;
            }
            $file = $files[$field];

            try {
                $uploadedFile = $this->fileUploader->upload($decorator->getUploadPathDirectory(), $file);
                if ($entity = $decorator->getEntity()) {
                    $this->propertyAccessor->setValue($entity, $field, $uploadedFile);
                }
                $entityChange = true;
            } catch (FileUploadException $e) {
            }
        }

        return $entityChange;
    }
}
