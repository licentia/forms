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

namespace Licentia\Forms\Block\Adminhtml\Forms;

/**
 * Class Edit
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @var \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory
     */
    protected $formElementsCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context                              $context
     * @param \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $formElementsCollection
     * @param \Magento\Framework\Registry                                        $registry
     * @param array                                                              $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $formElementsCollection,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->formElementsCollection = $formElementsCollection;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Forms';
        $this->_controller = 'adminhtml_forms';

        $form = $this->registry->registry('panda_form');

        $element = ($this->getRequest()->getParam('eid') || $this->getRequest()->getParam('element'));

        parent::_construct();

        if (!$form->getId()) {
            $this->buttonList->remove('reset');
        }
        if ($form->getId()) {
            $dataAR = [
                'label'   => __('View Entries'),
                'class'   => '',
                'onclick' => "setLocation('{$this->getUrl("pandaf/forms/entries",['id'=>$this->getRequest()->getParam('id')])}')",
            ];
            $this->buttonList->add('form_entries', $dataAR);
        }

        if (!$element) {
            $elements = $this->formElementsCollection->create()->addFieldToFilter('form_id', $form->getId());

            $this->buttonList->update('save', 'label', __('Save Form'));
            $this->buttonList->update('delete', 'label', __('Delete Form'));

            if ($elements->getSize() <= \Licentia\Forms\Model\Forms::FORMS_MAX_NUMBER_FIELDS &&
                $form->getId()
            ) {
                $this->getToolbar()
                     ->addChild(
                         'add-split-button',
                         'Magento\Backend\Block\Widget\Button\SplitButton',
                         [
                             'id'           => 'save-split-button',
                             'label'        => __('New Form Element'),
                             'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                             'button_class' => 'widget-button-save',
                             'options'      => $this->_getSaveSplitButtonOptions(),
                         ]
                     );
            }

            $this->buttonList->remove('save');
            $this->getToolbar()
                 ->addChild(
                     'save-split-button-',
                     'Magento\Backend\Block\Widget\Button\SplitButton',
                     [
                         'id'           => 'save-split-button',
                         'label'        => __('Save'),
                         'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                         'button_class' => 'widget-button-update',
                         'options'      => [
                             [
                                 'id'             => 'save-button',
                                 'label'          => __('Save'),
                                 'default'        => true,
                                 'data_attribute' => [
                                     'mage-init' => [
                                         'button' => [
                                             'event'  => 'saveAndContinueEdit',
                                             'target' => '#edit_form',
                                         ],
                                     ],
                                 ],
                             ],
                             [
                                 'id'             => 'save-continue-button',
                                 'label'          => __('Save & Close'),
                                 'data_attribute' => [
                                     'mage-init' => [
                                         'button' => [
                                             'event'  => 'save',
                                             'target' => '#edit_form',
                                         ],
                                     ],
                                 ],
                             ],
                         ],
                     ]
                 );
        }

        if ($element) {
            $this->buttonList->remove('save');
            $this->getToolbar()
                 ->addChild(
                     'save-split-button-',
                     'Magento\Backend\Block\Widget\Button\SplitButton',
                     [
                         'id'           => 'save-split-button',
                         'label'        => __('Save Element'),
                         'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                         'button_class' => 'widget-button-update',
                         'options'      => [
                             [
                                 'id'             => 'save-button',
                                 'label'          => __('Save Element'),
                                 'default'        => true,
                                 'data_attribute' => [
                                     'mage-init' => [
                                         'button' => [
                                             'event'  => 'saveAndContinueEdit',
                                             'target' => '#edit_form',
                                         ],
                                     ],
                                 ],
                             ],
                             [
                                 'id'             => 'save-continue-button',
                                 'label'          => __('Save Element & Close'),
                                 'data_attribute' => [
                                     'mage-init' => [
                                         'button' => [
                                             'event'  => 'save',
                                             'target' => '#edit_form',
                                         ],
                                     ],
                                 ],
                             ],
                         ],
                     ]
                 );

            $location = $this->getUrl(
                '*/*/delete',
                [
                    'eid'    => $this->getRequest()->getParam('eid'),
                    'tab_id' => 'element_section',
                ]
            );
            $locationReturn = $this->getUrl(
                '*/*/edit',
                [
                    'id'     => $this->getRequest()->getParam('id'),
                    'tab_id' => 'element_section',
                ]
            );

            $confirm = __(
                'Are you sure? This will remove saved data for this field. You can mark this element as inactive and it will not be displayed.'
            );

            $this->buttonList->update('delete', 'onclick', "deleteConfirm('{$confirm}','{$location}')");
            $this->buttonList->update('back', 'onclick', "setLocation('{$locationReturn}')");

            if (!$this->getRequest()->getParam('eid')) {
                $this->buttonList->remove('delete');
            }
        }
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {

        $options = [];
        $elementTypes = \Licentia\Forms\Model\Forms::ELEMENTS_TYPES;

        /** @var \Licentia\Forms\Model\Forms $form */
        $form = $this->registry->registry('panda_form');

        if (!$form->isFrontend()) {
            $exclude = ['checkbox', 'checkboxes', 'hidden'];

            $elementTypes = array_diff_key($elementTypes, array_flip($exclude));
        }

        $i = 0;
        foreach ($elementTypes as $key => $elementType) {
            $options[] = [
                'id'      => 'edit-button',
                'label'   => __($elementType),
                'onclick' => "window.location='" . $this->getUrl(
                        '*/*/edit',
                        [
                            'element' => $key,
                            'id'      => $this->getRequest()->getParam('id'),
                        ]
                    ) . "'",
                'default' => false,
            ];

            $i++;
        }

        return $options;
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {

        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        if ($form = $this->registry->registry('panda_form')->getId()) {
            return __("Edit Form '%1'", $this->escapeHtml($form->getName()));
        } else {
            return __('New Form');
        }
    }
}
