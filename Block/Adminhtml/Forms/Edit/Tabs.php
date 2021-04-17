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

namespace Licentia\Forms\Block\Adminhtml\Forms\Edit;

use Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Edit;
use Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Elements;
use Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Template;
use Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Information;

/**
 * Class Tabs
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        $this->registry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /** @noinspection MagicMethodsValidityInspection */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('forms_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Form Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        $current = $this->registry->registry('panda_form');

        $showTab = ($this->getRequest()->getParam('eid') || $this->getRequest()->getParam('element'));

        if (!$showTab) {
            $this->addTab(
                'information_section',
                [
                    'label'   => __('Information'),
                    'title'   => __('Information'),
                    'content' => $this->getLayout()
                                      ->createBlock(Information::class)
                                      ->toHtml(),
                ]
            );

            if ($current->getId()) {
                $this->addTab(
                    'template_section',
                    [
                        'label'   => __('Template'),
                        'title'   => __('Template'),
                        'content' => $this->getLayout()
                                          ->createBlock(Template::class)
                                          ->toHtml(),
                    ]
                );
            }
        }

        if ($current->getId() && !$showTab) {
            $this->addTab(
                "element_section",
                [
                    "label"   => __("Form Elements"),
                    "title"   => __("Form Elements"),
                    "content" => $this->getLayout()
                                      ->createBlock(Elements::class)
                                      ->toHtml(),
                ]
            );
        }

        if ($showTab) {
            $this->addTab(
                "edit_section",
                [
                    "label"   => __("Form Element"),
                    "title"   => __("Form Element"),
                    "content" => $this->getLayout()
                                      ->createBlock(Edit::class)
                                      ->toHtml(),
                ]
            );
        }


        return parent::_beforeToHtml();
    }
}
