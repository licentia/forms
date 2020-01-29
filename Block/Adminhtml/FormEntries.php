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
