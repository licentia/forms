<?php
/**
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
 * Class EditEntry
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms
 */
class EditEntry extends \Magento\Backend\Block\Widget\Form\Container
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

        $this->_mode = 'editEntry';
        $this->registry = $registry;
        $this->formElementsCollection = $formElementsCollection;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Forms';
        $this->_controller = 'adminhtml_forms';

        $form = $this->registry->registry('panda_form');

        parent::_construct();

        if (!$form->getId()) {
            $this->buttonList->remove('reset');
        }

        $this->buttonList->update('save', 'label', __('Save Entry'));
        $this->buttonList->update('delete', 'label', __('Delete Entry'));

        $confirmMsg = __('Are you sure?');

        if ($this->getRequest()->getParam('etid')) {
            $this->buttonList->update(
                'delete',
                'onclick',
                "deleteConfirm('$confirmMsg','{$this->getUrl("*/*/deleteEntry",[
                    'id'=>$form->getId(),
                    'deid'=>$this->getRequest()->getParam('etid')])}')"
            );
        } else {
            $this->buttonList->remove('delete');
        }
        $locationReturn = $this->getUrl(
            '*/*/entries',
            [
                'id' => $this->getRequest()->getParam('id'),
            ]
        );

        $this->buttonList->update('back', 'onclick', "setLocation('{$locationReturn}')");
        $this->buttonList->update('back', 'label', __('Back to Entries'));

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

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {

        return $this->getUrl(
            '*/*/saveEntry',
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

        if ($form = $this->registry->registry('panda_form_entry')->getId()) {
            return __("Edit Entry");
        } else {
            return __('New Entry');
        }
    }
}
