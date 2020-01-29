<?php

/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Forms\Block\Adminhtml\Forms\Edit;

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
    protected $registry;

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
                                      ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Information')
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
                                          ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Template')
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
                                      ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Elements')
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
                                      ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab\Edit')
                                      ->toHtml(),
                ]
            );
        }

        if ($this->getRequest()->getParam('tab_id')) {
            $this->setActiveTab($this->getRequest()->getParam('tab_id'));
        }

        return parent::_beforeToHtml();
    }
}
