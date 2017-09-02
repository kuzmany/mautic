<?php

/*
 * @copyright   Erasmus Student Network AISBL. 2017
 * @author      Gorka Guerrero Ruiz
 *
 * @link        http://esn.org
 *
 * @license     -
 */
?>

<table class="row xmlslot">
    <tbody>
    <tr class="xmlslottr">
        <th class="small-12 large-4 columns first">
            <table>
                <tr>
                    <th>
                        <img src="http://placehold.it/50x50" align="center" class="xmlslot-image">
                        <h2 class="xmlslot-title">Feature Three</h2>
                        <p class="xmlslot-desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rerum, quod
                            quam unde earum.</p>
                        <a href="#" class="xmlslot-button" target="_blank"
                           style="display:inline-block;text-decoration:none;border-color:#4e5d9d;border-width: 10px 20px;border-style:solid; text-decoration: none; -webkit-border-radius: 0x; -moz-border-radius: 0px; border-radius: 0px; background-color: #4e5d9d; display: inline-block;font-size: 16px; color: #ffffff; ">
                            I want this!
                        </a>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>

<div style="clear:both"></div>
<?php echo $view['assets']->includeScript(
    'plugins/MauticXMLSlotBundle/Assets/js/xmlslot.js',
    'mySlotListener',
    'mySlotListener'
); ?>
