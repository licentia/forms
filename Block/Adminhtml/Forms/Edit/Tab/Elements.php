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

namespace Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab;

/**
 * Class Grid
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms\Edit\Tab
 */
class Elements extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                            $context
     * @param \Magento\Backend\Helper\Data                                       $backendHelper
     * @param \Licentia\Panda\Helper\Data                                        $pandaHelper
     * @param \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                        $registry
     * @param array                                                              $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('panda_formElements_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setFilterVisibility(false);
        $this->setSortable(false);
        $this->setPagerVisibility(false);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $form = $this->registry->registry('panda_form');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('form_id', $form->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'element_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'element_id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'code',
            [
                'header' => __('Code'),
                'align'  => 'left',
                'index'  => 'code',
            ]
        );

        $this->addColumn(
            'type',
            [
                'header'  => __('Element Type'),
                'align'   => 'left',
                'index'   => 'type',
                'type'    => 'options',
                'options' => \Licentia\Forms\Model\Forms::ELEMENTS_TYPES,
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'placeholder',
            [
                'header' => __('Placeholder'),
                'align'  => 'left',
                'type'   => 'text',
                'index'  => 'placeholder',
            ]
        );

        $this->addColumn(
            'unique',
            [
                'header'  => __('Unique'),
                'align'   => 'left',
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'index'   => 'unique',
                'type'    => 'options',
            ]
        );

        $this->addColumn(
            'required',
            [
                'header'  => __('Required'),
                'align'   => 'left',
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'index'   => 'required',
                'type'    => 'options',
            ]
        );

        $this->addColumn(
            'disabled',
            [
                'header'  => __('Disabled'),
                'align'   => 'left',
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'index'   => 'disabled',
                'type'    => 'options',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header'  => __('Status'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'align'  => 'left',
                'index'  => 'sort_order',
                'type'   => 'number',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return bool
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/*/edit', ['eid' => $row->getId(), 'id' => $row->getFormId()]);
    }
}
