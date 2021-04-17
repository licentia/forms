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
 * Class Information
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab
 */
class Information extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected \Magento\Store\Model\System\Store $systemStore;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected \Licentia\Forms\Model\FormsFactory $formsFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Licentia\Forms\Model\FormsFactory      $formsFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->pandaHelper = $pandaHelper;
        $this->formsFactory = $formsFactory;
        $this->systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        $manageForm = $this->formsFactory->create();

        /** @var \Licentia\Forms\Model\Forms $current */
        $current = $this->_coreRegistry->registry('panda_form');

        $manageFormAvailable = false;
        if (!$manageForm->getId() || ($current->getId() != $manageForm->getId())) {
            $manageFormAvailable = true;
        }

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );
        $fieldset = $form->addFieldset('content_fieldset', ['legend' => __('Content')]);

        $fieldset->addField('entry_type', 'hidden', ['name' => 'entry_type', 'value' => $current->getEntryType()]);

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Internal Name'),
                'title'    => __('Internal Name'),
                "required" => true,
            ]
        );

        $html = '<script type="text/javascript">
                require(["jquery"],function ($){
                    toggleControlsValidateType = {
                        run: function() {
                            if($("#manage_subscription").val() == "1" ){
                                    $("div.admin__field.field.field-title").hide();
                                    $("div.admin__field.field.field-submit_label").hide();
                                    $("div.admin__field.field.field-success_page").hide();
                                    $("div.admin__field.field.field-success_message").hide();
                                    $("div.admin__field.field.field-max_entries").hide();
                                    $("div.admin__field.field.field-registered_only").hide();
                                    $("div.admin__field.field.field-update_label").hide();
                                    $("div.admin__field.field.field-can_edit").hide();
                                    $("div.admin__field.field.field-css_class").hide();
                                    $("div.admin__field.field.field-store_id").show();
                             }else{
                                    $("div.admin__field.field.field-title").show();
                                    $("div.admin__field.field.field-submit_label").show();
                                    $("div.admin__field.field.field-success_page").show();
                                    $("div.admin__field.field.field-success_message").show();
                                    $("div.admin__field.field.field-max_entries").show();
                                    $("div.admin__field.field.field-registered_only").show();
                                    $("div.admin__field.field.field-update_label").show();
                                    $("div.admin__field.field.field-can_edit").show();
                                    $("div.admin__field.field.field-css_class").show();
                                    $("div.admin__field.field.field-store_id").hide();
                            }
                        }
                    }
                    window.toggleControlsValidateType = toggleControlsValidateType;
                    $(function() {
                        toggleControlsValidateType.run();
                    });
                });
                </script>
         ';

        if ($current->getEntryType() === 'frontend') {
            $fieldset->addField(
                'manage_subscription',
                "select",
                [
                    "disabled" => !$manageFormAvailable,
                    "label"    => __('Display in manage subscription page?'),
                    "onchange" => 'toggleControlsValidateType.run();',
                    "options"  => [
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ],
                    "name"     => 'manage_subscription',
                    'note'     => 'If yes, this form will be shown in the newsletter preferences page when the subscriber clicks on "Manage Newsletter", from the email footer link, or navigates, in his account, to "Manage Newsletter". There is no need to add email input to map to the subscriber field as it is redundant',
                ]
            )
                     ->setAfterElementHtml($html);

            if (!$this->_storeManager->isSingleStoreMode()) {
                $options = $this->systemStore->getStoreValuesForForm();
                array_unshift($options, ['label' => __('-- Any --'), 'value' => 0]);
                $fieldset->addField(
                    'store_id',
                    'multiselect',
                    [
                        'name'     => 'store_id[]',
                        'label'    => __('Store View'),
                        'title'    => __('Store View'),
                        'required' => true,
                        'values'   => $options,
                    ]
                );
            }
        }

        if ($current->getEntryType() === 'frontend') {
            $fieldset->addField(
                'title',
                'text',
                [
                    'name'  => 'title',
                    'label' => __('Form Title'),
                    'title' => __('Form Title'),
                    "note"  => __('Customers will see this text'),
                ]
            );
        }

        $fieldset->addField(
            'code',
            'text',
            [
                'name'     => 'code',
                'label'    => __('Code'),
                'title'    => __('Code'),
                "required" => true,
                "class"    => 'small_input validate-code ',
                "note"     => 'Must be unique between all forms',
            ]
        );

        if ($current->getEntryType() === 'frontend') {
            $fieldset->addField(
                'submit_label',
                'text',
                [
                    'name'     => 'submit_label',
                    'label'    => __('Submit Button Text'),
                    'title'    => __('Submit Button Text'),
                    "required" => true,
                    'class'    => 'small_input',
                    'value'    => __('Send'),

                ]
            );

            $fieldset->addField(
                'success_page',
                'text',
                [
                    'name'  => 'success_page',
                    'label' => __('Success Page'),
                    'title' => __('Success Page'),
                    "note"  => __(
                        'Where to redirect the user after submitting the form. Full URL or relative path (http://www.mystore.com or /thanks-for-participating)'
                    ),
                ]
            );

            $fieldset->addField(
                'success_message',
                'text',
                [
                    'name'  => 'success_message',
                    'label' => __('Success Message'),
                    'title' => __('Success Message'),
                    "note"  => __('Success Message to show after form submission'),
                ]
            );

            $fieldset->addField(
                'notifications',
                'text',
                [
                    'name'  => 'notifications',
                    'label' => __('New Entry Email Notifications'),
                    'title' => __('New Entry Email Notifications'),
                    "note"  => __(
                        'Send notifications to the above emails when new entries are added. These emails override the ones in the configuration'
                    ),
                ]
            );
        }
        $fieldset->addField(
            'max_entries',
            'text',
            [
                'name'  => 'max_entries',
                'label' => __('Max Number of Entries'),
                'title' => __('Max Number of Entries'),
                "note"  => __('After this number, form submission will be disabled. 0 or empty to ignore'),
                'class' => 'small_input',
            ]
        );

        $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $fieldset->addField(
            'description',
            'editor',
            [
                'label'    => 'Description',
                'name'     => 'description',
                'style'    => 'height:10em',
                'required' => true,
                'config'   => $wysiwygConfig,
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

        if ($current->getEntryType() === 'frontend') {
            $html = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsValidatecan_edit = {
                    run: function() {
                        if($("#registered_only").val() != "0"){
                            $("#can_edit").parent().parent().show();
                        }else{
                            $("#can_edit").parent().parent().hide();
                        }
                    }
                }
                window.toggleControlsValidatecan_edit = toggleControlsValidatecan_edit;
                $(function() {
                    toggleControlsValidatecan_edit.run();
                });

                });
                </script>
                ';

            $fieldset->addField(
                'registered_only',
                "select",
                [
                    'onchange' => 'toggleControlsValidatecan_edit.run()',
                    "label"    => __('Logged In Customers Only?'),
                    "options"  => [
                        '1' => __('Yes - And they can view the form if they are not logged in'),
                        '2' => __('Yes - But they cannot view the form if they are not logged in'),
                        '0' => __('No - Anyone can submit the form'),
                    ],
                    "name"     => 'registered_only',
                    'note'     => 'In any case, Customer ID will be saved to the entries table, if the customer is logged in',
                ]
            )
                     ->setAfterElementHtml($html);

            $html = '<script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsValidatecanupdate_label = {
                    run: function() {
                        if($("#can_edit").val() != "0"){
                            $("#update_label").parent().parent().show();
                        }else{
                            $("#update_label").parent().parent().hide();
                        }
                    }
                }
                window.toggleControlsValidatecanupdate_label = toggleControlsValidatecanupdate_label;
                $(function() {
                    toggleControlsValidatecanupdate_label.run();
                });

                });
                </script>
                ';

            $fieldset->addField(
                'can_edit',
                "select",
                [
                    'onchange' => 'toggleControlsValidatecanupdate_label.run()',
                    "label"    => __('Can Customers Edit their Entry?'),
                    "options"  => [
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ],
                    "name"     => 'can_edit',
                    'note'     => 'If yes, customers will be able to edit the entry they submitted. Only one entry per customer will be allowed',
                ]
            )
                     ->setAfterElementHtml($html);

            $fieldset->addField(
                'update_label',
                'text',
                [
                    'name'     => 'update_label',
                    'label'    => __('Update Button Text'),
                    'title'    => __('Update Button Text'),
                    "required" => true,
                    "note"     => 'When customers are allowed to edit their entry',
                    'class'    => 'small_input',
                    'value'    => __('Update'),

                ]
            );
        }

        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                'date_format' => $dateFormat,
                'required'    => true,
                'label'       => __('Active From Date'),
            ]
        );

        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                'date_format' => $dateFormat,
                'required'    => true,
                'label'       => __('Active To Date'),
            ]
        );
        $fieldset->addField(
            'css_class',
            'text',
            [
                'name'  => 'css_class',
                'label' => __('CSS Class'),
                'title' => __('CSS Class'),
                "note"  => __('This class will be added to the form element'),
                'class' => 'small_input',
            ]
        );

        $this->setForm($form);

        if ($current) {
            $form->addValues($current->getData());
        }

        return parent::_prepareForm();
    }
}
