<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\FormBundle\Event as Events;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\SubmissionEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\FormBundle\Model\SubmissionModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticAddressValidatorBundle\AddressValidatorEvents;
use MauticPlugin\MauticAddressValidatorBundle\Helper\AddressValidatorHelper;

/**
 * Class FormSubscriber.
 */
class FormSubscriber extends CommonSubscriber
{
    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * @var AddressValidatorHelper ;
     */
    protected $addressValidatorHelper;

    /**
     * @var SubmissionModel
     */
    protected $submissionModel;

    /**
     * FormSubscriber constructor.
     *
     * @param LeadModel              $leadModel
     * @param CoreParametersHelper   $coreParametersHelper
     * @param AddressValidatorHelper $addressValidatorHelper
     * @param SubmissionModel        $submissionModel
     */
    public function __construct(
        LeadModel $leadModel,
        CoreParametersHelper $coreParametersHelper,
        AddressValidatorHelper $addressValidatorHelper,
        SubmissionModel $submissionModel
    ) {
        $this->leadModel              = $leadModel;
        $this->coreParametersHelper   = $coreParametersHelper;
        $this->addressValidatorHelper = $addressValidatorHelper;
        $this->submissionModel        = $submissionModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD                       => ['onFormBuilder', 0],
            FormEvents::FORM_ON_SUBMIT                      => ['onFormSubmit', 0],
            AddressValidatorEvents::ON_FORM_VALIDATE_ACTION => ['onFormValidate', 0],
        ];
    }

    /**
     * Trigger campaign event for when a form is submitted.
     *
     * @param SubmissionEvent $event
     */
    public function onFormSubmit(SubmissionEvent $event)
    {
        $form       = $event->getSubmission()->getForm();
        $fields     = $form->getFields();
        $lead       = $event->getLead();
        $submission = $event->getSubmission();
        $results    = $event->getResults();
        $props      = [];

        // update form results
        /** @var SubmissionRepository $repo */
        $repo             = $this->submissionModel->getRepository();
        $resultsTableName = $repo->getResultsTableName($form->getId(), $form->getAlias());
        $tableKeys        = ['submission_id' => $submission->getId()];

        foreach ($event->getFields() as $field) {
            if ($field['type'] == 'plugin.addressvalidator') {
                $addressValidatorFieldAlias = $field['alias'];
                $data                       = $event->getRequest()->get('mauticform')[$addressValidatorFieldAlias];
                if (empty($data['addressvalidated'])) {
                    $data['addressvalidated'] = 'No';
                }
                /* @var \Mautic\FormBundle\Entity\Field $f */
                if (!empty($data)) {
                    foreach ($fields as $f) {
                        if ($f->getAlias() == $addressValidatorFieldAlias) {
                            $props = [];
                            foreach ($f->getProperties() as $key => $property) {
                                if (strpos($key, 'label') !== false || strpos($key, 'leadField') !== false) {
                                    $newKey = strtolower(str_ireplace(['label', 'leadField'], ['', ''], $key));
                                    if ($newKey) {
                                        $props[$newKey][str_ireplace($newKey, '', $key)] = $property;
                                    }
                                }
                            }
                        }
                    }
                    foreach ($data as $key => $value) {
                        if (in_array($key, array_keys($props))) {
                            $matchLeadField = $props[$key]['leadField'];
                            if ($matchLeadField) {
                                $var = 'set'.ucfirst($matchLeadField);
                                $lead->$var($value);
                            }
                        }
                    }
                    if (!empty($lead->getChanges())) {
                        $this->leadModel->saveEntity($lead);
                    }
                    // update addres field
                    $results[$addressValidatorFieldAlias] = $results[$addressValidatorFieldAlias] = http_build_query($data,
                        ',', '|');
                    $this->em
                        ->getConnection()
                        ->update($resultsTableName, $results, $tableKeys);
                }
            }
        }
    }

    /**
     * Add a lead generation action to available form submit actions.
     *
     * @param FormBuilderEvent $event
     */
    public function onFormBuilder(FormBuilderEvent $event)
    {
        if ($this->addressValidatorHelper->validation(true)) {
            $action = [
                'label'          => 'mautic.plugin.field.addressvalidator',
                'formType'       => 'addressvalidator',
                'template'       => 'MauticAddressValidatorBundle:SubscribedEvents\Field:addressvalidator.html.php',
                'builderOptions' => [
                    'addLeadFieldList'       => false,
                    'addDefaultValue'        => false,
                    'addSaveResult'          => true,
                    'addShowLabel'           => true,
                    'addHelpMessage'         => false,
                    'addLabelAttributes'     => false,
                    'addInputAttributes'     => false,
                    'addBehaviorFields'      => false,
                    'addContainerAttributes' => false,
                    'allowCustomAlias'       => true,
                    'labelText'              => false,
                    'addIsRequired'          => false,
                ],
            ];

            $event->addFormField('plugin.addressvalidator', $action);

            $validator = [
                'eventName' => AddressValidatorEvents::ON_FORM_VALIDATE_ACTION,
                'fieldType' => 'plugin.addressvalidator',
            ];

            $event->addValidator('plugin.addressvalidator.validate', $validator);
        }
    }

    /**
     * @param Events\ValidationEvent $event
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function onFormValidate(Events\ValidationEvent $event)
    {
        $field = $event->getField();

        if ($field->getType() == 'plugin.addressvalidator') {
            $data = $event->getValue();

            // spam detection
            if (!empty($data['address4'])) {
                return $event->failedValidation(
                    $this->translator->trans('plugin.addressvalidator.detect.spam')
                );
            }

            $dataValues = array_filter($data);

            // force validation, not continue anyway
            $forceValidation = false;
            if (!empty($field->getProperties()['validatorRequired'])) {
                $forceValidation = true;
            } elseif (empty($field->getProperties()['validatorRequired']) &&
                !empty($data['toogle']) && !empty($dataValues)
            ) {
                $forceValidation = true;
            } elseif (empty($field->getProperties()['validatorRequired']) && !empty($dataValues)) {
                $forceValidation = true;
            }
            // empty address
            if ($forceValidation && empty($dataValues)) {
                return $event->failedValidation(
                    $this->translator->trans('plugin.addressvalidator.form.empty')
                );
            }

            if ($forceValidation) {
                $values = $this->addressValidatorHelper->parseDataFromRequest($event->getValue());
                $result = $this->addressValidatorHelper->validation(false, null, $values);
                if (!empty($result)) {
                    $result = \GuzzleHttp\json_decode($result, true);
                    if (empty($result['status'])) {
                        $result['status'] = '';
                    }
                    if ($result['status'] == 'INVALID') {
                        return $event->failedValidation(
                            $this->translator->trans('plugin.addressvalidator.address.is.not.valid')
                        );
                    } elseif ($result['status'] == 'SUSPECT') {
                        if (!isset($data['correctedaddress'])) {
                            return $event->failedValidation(
                                \GuzzleHttp\json_encode($result)
                            );
                        }
                    }
                } else {
                    return $event->failedValidation(
                        $this->translator->trans('plugin.addressvalidator.form.empty')
                    );
                }
            }
        }
    }
}
