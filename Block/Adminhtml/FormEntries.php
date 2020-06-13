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

namespace Licentia\Forms\Block\Adminhtml;

/**
 * Class FormEntries
 *
 * @package Licentia\Forms\Block\Adminhtml
 */
class FormEntries extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * FormEntries constructor.
     *
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {

        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Forms';
        $this->_controller = 'adminhtml_forms';
        $this->_headerText = __('Forms');
        parent::_construct();

        /** @var \Licentia\Forms\Model\Forms $form */
        $form = $this->registry->registry('panda_form');

        if ($form->isFrontend()) {
            $this->buttonList->remove('add');
        }

        if (count($form->getActiveElements()) == 0) {
            $this->buttonList->remove('add');
        }

        $this->buttonList->update('add', 'label', __('New Entry'));
        $this->buttonList->update(
            'add',
            'onclick',
            "setLocation('{$this->getUrl("pandaf/forms/newEntry",['id'=>$this->getRequest()->getParam('id')])}');
            return false;"
        );

        $dataAR = [
            'label'   => __('Back to Form'),
            'class'   => 'back',
            'onclick' => "setLocation('{$this->getUrl("pandaf/forms/edit",
            [
                'id'=>$this->getRequest()->getParam('id'),
                ]
                )}')",
        ];

        $this->buttonList->add('return_form', $dataAR);
    }
}
