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

namespace Mautic\LeadBundle\Uploader\Decorator;

use Mautic\CoreBundle\Uploader\Decorator\AbstractUploaderDecorator;

class LeadNoteUploaderDecorator extends AbstractUploaderDecorator
{
    const FIELDS           = ['attachment'];
    const FORM             = 'leadnote';
    const UPLOAD_DIRECTORY = 'note';

    /**
     * @return string
     */
    public function getUploadDirectory()
    {
        return ['files', self::UPLOAD_DIRECTORY, $this->getEntity()->getId()];
    }
}
