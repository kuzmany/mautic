<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!empty($inForm)):
    $html = <<<HTML
<div class="mauticform-row">
    <label class="text-muted">{$field['label']}</label>
    <p>{$view['translator']->trans('mautic.plugin.addressvalidators.field.helper')}
</p>
</div>
HTML;
else:
endif;
echo $html;