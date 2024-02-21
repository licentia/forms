<?php

/*
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
 *
 */

namespace Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab;

/**
 * Class Elements
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Licentia\Forms\Model\FormElementsFactory
     */
    protected $formElementsFactory;

    /**
     * @var \Licentia\Panda\Model\Source\CustomerAttributes
     */
    protected $customerAttributes;

    /**
     * Edit constructor.
     *
     * @param \Licentia\Panda\Model\Source\CustomerAttributes $customerAttributes
     * @param \Magento\Backend\Block\Template\Context         $context
     * @param \Magento\Framework\Registry                     $registry
     * @param \Magento\Framework\Data\FormFactory             $formFactory
     * @param \Licentia\Panda\Helper\Data                     $pandaHelper
     * @param \Magento\Store\Model\System\Store               $systemStore
     * @param \Licentia\Forms\Model\FormsFactory              $formsFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory        $extraFieldsFactory
     * @param \Licentia\Forms\Model\FormElementsFactory       $formElementsFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config               $wysiwygConfig
     * @param array                                           $data
     */
    public function __construct(
        \Licentia\Panda\Model\Source\CustomerAttributes $customerAttributes,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->customerAttributes = $customerAttributes;
        $this->pandaHelper = $pandaHelper;
        $this->formsFactory = $formsFactory;
        $this->systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->formElementsFactory = $formElementsFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Forms\Model\FormElements $current */
        $current = $this->_coreRegistry->registry('panda_form_element');

        /** @var \Licentia\Forms\Model\Forms $currentForm */
        $currentForm = $this->_coreRegistry->registry('panda_form');

        $type = $this->getRequest()->getParam('element');
        if ($current->getType()) {
            $type = $current->getType();
        }

        $pathProtect = trim($this->_scopeConfig->getValue('panda_forms/forms/protect'));

        $current->setData('options', str_replace(',', "\n", $current->getOptions()));

        $eid = $this->getRequest()->getParam('eid');

        $mapFields = [
            'placeholder'      => [
                'text',
                'textarea',
                'email',
                'phone',
                'number',
                'url',
            ],
            'hint'             => [
                'text',
                'textarea',
                'image',
                'grid',
                'rating',
                'file',
                'email',
                'phone',
                'number',
                'url',
                'date',
                'select',
                'country',
                'file',
                'checkbox',
                'radios',
                'checkboxes',
            ],
            'pattern'          => [
                'text',
                'textarea',
                'email',
                'phone',
                'number',
                'url',
                'date',
            ],
            'css_class'        => [
                'text',
                'textarea',
                'email',
                'phone',
                'number',
                'file',
                'url',
                'date',
                'select',
                'country',
                'file',
                'checkbox',
                'radios',
                'checkboxes',
                'grid',
                'rating',
                'image',
            ],
            'default'          => [
                'text',
                'textarea',
                'email',
                'phone',
                'number',
                'country',
                'hidden',
                'select',
                'url',
                'date',
            ],
            'checked'          => [
                'checkbox',
            ],
            'multiple'         => [
                'file',
                'image',
            ],
            'required'         => [
                'text',
                'textarea',
                'file',
                'email',
                'phone',
                'number',
                'url',
                'date',
                'select',
                'country',
                'file',
                'checkbox',
                'radios',
                'checkboxes',
                'grid',
                'rating',
                'image',
            ],
            'min_length'       => [
                'text',
                'textarea',
                'phone',
                'number',
            ],
            'max_length'       => [
                'text',
                'textarea',
                'phone',
                'number',
            ],
            'disabled'         => [
                'text',
                'textarea',
                'email',
                'phone',
                'number',
                'url',
                'date',
                'select',
                'country',
                'radios',
                'checkboxes',
            ],
            'unique'           => [
                'text',
                'email',
                'phone',
                'number',
                'url',
            ],
            'options'          => [
                'radios',
                'checkboxes',
                'select',
            ],
            'stars'            => [
                'rating',
            ],
            'max_width'        => [
                'image',
            ],
            'min_width'        => [
                'image',
            ],
            'min_height'       => [
                'image',
            ],
            'max_height'       => [
                'image',
            ],
            'resize'           => [
                'image',
            ],
            'max_number'       => [
                'number',
            ],
            'min_number'       => [
                'number',
            ],
            'max_date'         => [
                'date',
            ],
            'min_date'         => [
                'date',
            ],
            'map'              => [
                'email',
                'phone',
                'date',
                'country',
                'text',
                'radios',
                'select',
                'checkboxes',
                'number',
                'url',
            ],
            'map_customer'     => [
                'email',
                'phone',
                'date',
                'country',
                'text',
                'radios',
                'select',
                'checkboxes',
                'number',
                'url',
            ],
            'checkbox'         => [
                'checkbox',
            ],
            'html'             => [
                'html',
            ],
            'params'           => [
                'hidden',
            ],
            'extensions'       => [
                'file',
                'image',
            ],
            'protected'        => [
                'file',
                'image',
            ],
            'encrypted'        => [
                'file',
                'image',
            ],
            'email_validation' => [
                'email',
            ],
        ];

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );

        $title = __('Add Element of type %1 to Form: %2', $type, $currentForm->getName());
        if ($current->getId()) {
            $title = __('Edit Element of type %1 in Form: %2', $type, $currentForm->getName());
        }

        $fieldset = $form->addFieldset('content_fieldset', ['legend' => $title]);

        $fieldset->addField(
            'type',
            'hidden',
            [
                'value' => $type,
                'name'  => 'type',
            ]
        );

        $fieldset->addField(
            'eid',
            'hidden',
            [
                'value' => $eid,
                'name'  => 'eid',
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true,
                'note'     => __('This is the name the customers will see'),
            ]
        );

        $fieldset->addField(
            'sort_order',
            "text",
            [
                "label"    => __('Sort Order'),
                "name"     => 'sort_order',
                "required" => true,
                "class"    => 'validate-number small_input',
                'note'     => __("The order in which the element should be displayed"),
            ]
        );

        $fieldset->addField(
            'is_active',
            "select",
            [
                "label"   => __('Status'),
                "options" => ['1' => __('Active'), '0' => __('Inactive')],
                "name"    => 'is_active',
            ]
        );

        if ($currentForm->isFrontend()) {
            $fieldset->addField(
                'show_in_frontend',
                "select",
                [
                    "label"   => __('Show In Frontend?'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'show_in_frontend',
                    "value"   => '1',
                ]
            );
        }

        $fieldset->addField(
            'show_in_grid',
            "select",
            [
                "label"   => __('Show In Admin Grid?'),
                "options" => ['1' => __('Yes'), '0' => __('No')],
                "name"    => 'show_in_grid',
                "value"   => '1',
            ]
        );

        if (in_array($type, $mapFields['stars'])) {
            $fieldset->addField(
                'stars',
                "select",
                [
                    "label"   => __('How many stars to display?'),
                    "options" => [5 => 5, 10 => 10],
                    "name"    => 'stars',
                ]
            );
        }

        if (in_array($type, $mapFields['html'])) {
            $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
            $fieldset->addField(
                'html',
                'editor',
                [
                    'label'    => 'HTML',
                    'name'     => 'html',
                    'style'    => 'height:10em',
                    'required' => true,
                    'config'   => $wysiwygConfig,
                ]
            );
        }

        if (in_array($type, $mapFields['extensions'])) {
            $fieldset->addField(
                'extensions',
                "text",
                [
                    "label"    => __('Allowed File Extensions'),
                    "name"     => 'extensions',
                    "required" => true,
                    'note'     => __("Separate multiple with commas. eg: csv,pdf,png"),
                    'class'    => 'small_input',
                ]
            );

            $fieldset->addField(
                'max_size',
                "text",
                [
                    "label"    => __('Max File Size'),
                    "name"     => 'max_size',
                    "required" => true,
                    'note'     => __("In MB"),
                    'class'    => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['protected'])) {

            $extraNote = '';

            if (!$pathProtect) {
                $extraNote = "<br><br>WARNING: Please define the save path in 'System/Green Flying Panda/Configuration/Forms'";
            }

            $fieldset->addField(
                'protected',
                'select',
                [
                    'name'     => 'protected',
                    'type'     => 'options',
                    'value'    => 0,
                    'options'  => [1 => __('Yes'), 0 => __('No')],
                    'label'    => __('Protect from direct Access'),
                    "onchange" => "toggleControlsValidateProtect.run()",
                    'note'     => __(
                        "This file will be moved to the save patch specified in 'System/Green Flying Panda/Configuration/Forms', so no direct access trough an URL is possible" . $extraNote
                    ),
                ]
            );

            $html = '
                <script type="text/javascript">

                require(["jquery"],function ($){
                toggleControlsValidateProtect = {
                    run: function() {
                        if($("#protected").val() == "1" ){
                            $("div.admin__field.field.field-encrypted").show();
                        }else{ 
                            $("div.admin__field.field.field-encrypted").hide();
                        }
                    }
                }
                window.toggleControlsValidateProtect = toggleControlsValidateProtect;
                $(function() {
                    toggleControlsValidateProtect.run();
                });

                });
                </script>
                ';

            $fieldset->addField(
                'encrypted',
                'select',
                [
                    'name'    => 'encrypted',
                    'type'    => 'options',
                    'value'   => 0,
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'label'   => __('Encrypt'),
                    'note'    => __("Encrypt file contents. If enabled, the only way to view the file is from the Admin Interface."),
                ]
            )
                     ->setAfterElementHtml($html);
        }

        if (in_array($type, $mapFields['placeholder'])) {
            $note = 'This text will be displayed inside the form element to help the user understand the information he needs to add';

            if ($type == 'checkbox') {
                $note = 'This text will be displayed on the right side of the checkbox';
            }

            $fieldset->addField(
                'placeholder',
                "text",
                [
                    "label" => __('Placeholder'),
                    "name"  => 'placeholder',
                    'note'  => __(
                        $note
                    ),
                ]
            );
        }

        if ($currentForm->isFrontend() && in_array($type, $mapFields['email_validation'])) {
            $existingMaps = $this->formElementsFactory->create()
                                                      ->getCollection()
                                                      ->addFieldToFilter('form_id', $currentForm->getId())
                                                      ->addFieldToFilter('type', 'email')
                                                      ->addFieldToFilter('email_validation', '1');

            if ($current->getId()) {
                $existingMaps->addFieldToFilter('element_id', ['neq' => $current->getId()]);
            }

            if ($existingMaps->getSize() == 0) {
                $html = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsValidateEmail = {
                    run: function() {
                        if($("#email_validation").val() != "0" && $("#email_validation").val() != "undefined"){
                            $("#link_expiration").parent().parent().show();
                        }else{
                            $("#link_expiration").parent().parent().hide();
                        }
                    }
                }
                window.toggleControlsValidateEmail = toggleControlsValidateEmail;
                $(function() {
                    toggleControlsValidateEmail.run();
                });

                });
                </script>
                ';

                $fieldset->addField(
                    'email_validation',
                    "select",
                    [
                        "label"    => __('Require Email Validation'),
                        'onchange' => 'toggleControlsValidateEmail.run()',
                        "options"  => ['1' => __('Yes'), '0' => __('No')],
                        "name"     => 'email_validation',
                        "note"     => 'If yes, an email will be sent to the user to confirm the email/entry submission',
                    ]
                )
                         ->setAfterElementHtml($html);

                $fieldset->addField(
                    'link_expiration',
                    "text",
                    [
                        "label" => __('Link Expiration'),
                        "name"  => 'link_expiration',
                        'note'  => __(
                            "In Hours. No entries will be validated if the form is inactive. Consider add a hint to the email field stating this to the customer"
                        ),
                    ]
                );
            }
        }

        if ($currentForm->isFrontend() && in_array($type, $mapFields['params'])) {
            $fieldset->addField(
                'params',
                "text",
                [
                    "label" => __('URL Params'),
                    "name"  => 'params',
                    'note'  => __(
                        'Get the value form an URL param and insert it here. Separate multiple params with a comma'
                    ),
                    'class' => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['min_length'])) {
            $fieldset->addField(
                'min_length',
                "text",
                [
                    "label" => __('Min Length'),
                    "name"  => 'min_length',
                    'note'  => __('Minimum length for the input content'),
                    'class' => 'small_input validate-number',
                ]
            );
        }
        if (in_array($type, $mapFields['max_length'])) {
            $fieldset->addField(
                'max_length',
                "text",
                [
                    "label" => __('Max Length'),
                    "name"  => 'max_length',
                    'note'  => __('Maximum length for the input content'),
                    'class' => 'small_input validate-number',
                ]
            );
        }

        if (in_array($type, $mapFields['min_number'])) {
            $fieldset->addField(
                'min_number',
                "text",
                [
                    "label" => __('Min Number'),
                    "name"  => 'min_number',
                    'note'  => __('Highest number the customer is allowed to insert'),
                    'class' => 'small_input validate-number',
                ]
            );
        }

        if (in_array($type, $mapFields['max_number'])) {
            $fieldset->addField(
                'max_number',
                "text",
                [
                    "label" => __('Max Number'),
                    "name"  => 'max_number',
                    'note'  => __('Lowest number the customer is allowed to insert'),
                    'class' => 'small_input validate-number',
                ]
            );
        }

        $dateFormat = $this->_localeDate->getDateFormat();
        if (in_array($type, $mapFields['min_date'])) {
            $fieldset->addField(
                'min_date',
                "date",
                [
                    "label"       => __('Min Date'),
                    "name"        => 'min_date',
                    'note'        => __('Lowest Date the customer is allowed to insert'),
                    'class'       => 'small_input',
                    'date_format' => $dateFormat,
                ]
            );
        }

        if (in_array($type, $mapFields['max_date'])) {
            $fieldset->addField(
                'max_date',
                "date",
                [
                    "label"       => __('Max Date'),
                    'date_format' => $dateFormat,
                    "name"        => 'max_date',
                    'note'        => __('Highest Date the customer is allowed to insert'),
                    'class'       => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['max_width'])) {
            $fieldset->addField(
                'max_width',
                "text",
                [
                    "label" => __('Max Image Width (px)'),
                    "name"  => 'max_width',
                    'class' => 'small_input validate-digits',
                ]
            );
        }

        if (in_array($type, $mapFields['min_width'])) {
            $fieldset->addField(
                'min_width',
                "text",
                [
                    "label" => __('Min Image Width (px)'),
                    "name"  => 'min_width',
                    'class' => 'small_input validate-digits',
                ]
            );
        }

        if (in_array($type, $mapFields['max_height'])) {
            $fieldset->addField(
                'max_height',
                "text",
                [
                    "label" => __('Max Image Height (px)'),
                    "name"  => 'max_height',
                    'class' => 'small_input validate-digits',
                ]
            );
        }
        if (in_array($type, $mapFields['min_height'])) {
            $fieldset->addField(
                'min_height',
                "text",
                [
                    "label" => __('Min Image Height (px)'),
                    "name"  => 'min_height',
                    'class' => 'small_input validate-digits',
                ]
            );
        }

        if (in_array($type, $mapFields['resize'])) {
            $fieldset->addField(
                'resize',
                "select",
                [
                    "label"   => __('Resize Image'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'resize',
                    "note"    => 'Resize Image to the max allowed dimensions',
                ]
            );
        }

        if (in_array($type, $mapFields['multiple'])) {
            $fieldset->addField(
                'allow_multiple',
                "text",
                [
                    "label"   => __('Max. files allowed to upload'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'allow_multiple',
                    'note'    => __("Don't save more files/images than this number"),
                    'class'   => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['hint'])) {
            $note = 'Extra information to be displayed below the form element';
            if ($type == 'checkbox') {
                $note = 'This text will be displayed on the right side of the checkbox';
            }

            $fieldset->addField(
                'hint',
                "text",
                [
                    "label" => __('Hint'),
                    "name"  => 'hint',
                    'note'  => __($note),
                ]
            );
        }

        if (in_array($type, $mapFields['unique'])) {
            $fieldset->addField(
                'unique',
                "select",
                [
                    "label"   => __('Unique Value'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'unique',
                    "note"    => 'This field value must be unique between all form submissions',
                ]
            );
        }

        if ($currentForm->isFrontend() && in_array($type, $mapFields['map'])) {

            /** @var \Licentia\Panda\Model\ResourceModel\ExtraFields\Collection $mapCollection */
            $mapCollection = $this->extraFieldsFactory->create()->getCollection();

            $existingMaps = $this->formElementsFactory->create()
                                                      ->getCollection()
                                                      ->addFieldToFilter('form_id', $currentForm->getId())
                                                      ->addFieldToFilter('map', ['notnull' => true]);

            $options['0'] = __('No Mapping');

            if ($type != 'email' && $type != 'cellphone') {
                if ($current->getId()) {
                    $existingMaps->addFieldToFilter('element_id', ['neq' => $current->getId()]);
                }

                $ignore = [];
                foreach ($existingMaps as $item) {
                    $ignore[] = $item->getData('entry_code');
                }

                if ($ignore) {
                    $mapCollection->addFieldToFilter('entry_code', ['nin' => $ignore]);
                }
            }

            if ($type == 'email') {
                $mapCollection->addFieldToFilter('type', 'email');

                $existingMaps->addFieldToFilter('map', 'email');
                if ($existingMaps->getSize() == 0) {
                    $options['email'] = __('Subscriber Email');
                }
            } elseif ($type == 'cellphone') {
                $mapCollection->addFieldToFilter('type', 'cellphone');

                $existingMaps->addFieldToFilter('map', 'cellphone');
                if ($existingMaps->getSize() == 0) {
                    $options['cellphone'] = __('Subscriber Cellphone');
                }
            } elseif (in_array($type, ['radios', 'checkboxes', 'select', 'options'])) {
                $mapCollection->addFieldToFilter('type', 'options');
            } elseif ($type == 'number') {
                $mapCollection->addFieldToFilter('type', 'number');
            } elseif (in_array($type, ['date'])) {
                $mapCollection->addFieldToFilter('type', 'date');
            } else {
                $existingMapsLastName = clone $existingMaps;
                $existingMapsLastDob = clone $existingMaps;

                if ($type == 'text') {
                    $existingMaps->addFieldToFilter('map', 'firstname');
                    if ($existingMaps->getSize() == 0) {
                        $options['firstname'] = __('Subscriber First Name');
                    }
                }

                if ($type == 'text') {
                    $existingMapsLastName->addFieldToFilter('map', 'lastname');
                    if ($existingMapsLastName->getSize() == 0) {
                        $options['lastname'] = __('Subscriber Last Name');
                    }
                }
                if ($type == 'text') {
                    $existingMapsLastDob->addFieldToFilter('map', 'dob');
                    if ($existingMapsLastName->getSize() == 0) {
                        $options['dob'] = __('Subscriber Date of Birth');
                    }
                }

                $mapCollection->addFieldToFilter('type', 'text');
            }
            $options = $options + $mapCollection->toOptionHash();

            if (count($options) == 1) {
                $options = ['0' => __('No Subscriber Extra Fields Available to Map for this Type of Field')];
            }

            $htmlOptions = '';
            if (in_array($type, ['radios', 'checkboxes', 'options', 'select'])) {
                $htmlOptions = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsOptions = {
                    run: function() {
                        if($("#map").val() != "0" ){
                            $("#options").parent().parent().hide();
                            $("#default").parent().parent().hide();
                        }else{
                            $("#options").parent().parent().show();
                            $("#default").parent().parent().show();
                        }
                    }
                }
                window.toggleControlsOptions = toggleControlsOptions;
                $(function() {
                    toggleControlsOptions.run();
                });

                });
                </script>
                ';
            }

            $fieldset->addField(
                'map',
                "select",
                [
                    'onchange' => 'toggleControlsOptions.run()',
                    "label"    => __('Map to Subscriber Field'),
                    "options"  => $options,
                    "name"     => 'map',
                    "note"     => 'Update subscriber info with the value from this field',
                ]
            )
                     ->setAfterElementHtml($htmlOptions);
        }

        if ($currentForm->isFrontend() &&
            in_array($type, $mapFields['map_customer']) &&
            $currentForm->getRegisteredOnly() > 0) {
            $fieldset->addField(
                'map_customer',
                "select",
                [
                    "label"  => __('Map to Customer Field'),
                    "values" => $this->customerAttributes->toOptionArray(),
                    "name"   => 'map_customer',
                    "note"   => 'Update customer info with the value from this field',
                ]
            );
        }

        if (in_array($type, $mapFields['options'])) {
            $fieldset->addField(
                'options',
                "textarea",
                [
                    "label" => __('Field Options'),
                    "name"  => 'options',
                    'note'  => __("One Per Line"),
                ]
            );
        }

        if (in_array($type, $mapFields['required'])) {
            $fieldset->addField(
                'required',
                "select",
                [
                    "label"   => __('Required'),
                    "options" => ['1' => __('Yes'), '0' => __('No')],
                    "name"    => 'required',
                    'note'    => __("Mark this field as required."),
                ]
            );
        }

        if (in_array($type, $mapFields['default'])) {
            $fieldset->addField(
                'default',
                "text",
                [
                    "label" => __('Default Value'),
                    "name"  => 'default',
                    'note'  => __("The default value for the field. This value can be changed by the user"),
                    'class' => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['css_class'])) {
            $fieldset->addField(
                'css_class',
                "text",
                [
                    "label" => __('CSS Class'),
                    "name"  => 'css_class',
                    'note'  => __("Specific CSS classes to add to the element wrapper"),
                    'class' => 'small_input',
                ]
            );
        }

        if (in_array($type, $mapFields['disabled'])) {
            $fieldset->addField(
                'disabled',
                'select',
                [
                    'name'    => 'disabled',
                    'type'    => 'options',
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'label'   => __('Disabled'),
                    'note'    => __(
                        "This field will still be displayed to customers, but they won't be able to change it"
                    ),
                ]
            );
        }

        if (in_array($type, $mapFields['checked'])) {
            $fieldset->addField(
                'checked',
                'select',
                [
                    'name'    => 'checked',
                    'type'    => 'options',
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'label'   => __('Checked'),
                    'note'    => __("If by default this checkbox should be checked"),
                ]
            );
        }

        if (in_array($type, $mapFields['pattern'])) {
            $fieldset->addField(
                'pattern',
                "text",
                [
                    "label" => __('Validation Pattern'),
                    "name"  => 'pattern',
                    'note'  => __('REGEX to validate form value. (advanced users only)'),
                ]
            );
        }

        $fieldset->addField(
            'code',
            'text',
            [
                'name'     => 'code',
                'disabled' => $current->getCode() ? true : false,
                'label'    => __('Code'),
                'title'    => __('Code'),
                'class'    => __('validate-code'),
                'note'     => __('Element Identifier. Must be unique per form. Cannot be changed after'),
            ]
        );

        $this->setForm($form);

        if ($current) {
            $form->addValues($current->getData());
        }

        if (!$current->getId()) {
            $form->addValues(['is_active' => 1]);
        }

        return parent::_prepareForm();
    }
}
