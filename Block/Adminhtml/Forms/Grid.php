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

use Licentia\Forms\Model\Forms;

/**
 * Class Grid
 *
 * @package Licentia\Forms\Block\Adminhtml\FormEntries
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;

    /**
     * @param \Magento\Framework\Url                                            $url
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Licentia\Panda\Helper\Data                                       $pandaHelper
     * @param \Licentia\Forms\Model\FormsFactory                                $formsFactory
     * @param \Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                       $registry
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Framework\Url $url,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->urlHelper = $url;
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->formsFactory = $formsFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('panda_entries_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        if ($sid = $this->getRequest()->getParam('subscriber_id')) {
            $this->setDefaultFilter(['subscriber_id' => $sid]);
        }
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {

        $collection = $this->collectionFactory->create();

        $form = $this->registry->registry('panda_form');
        $collection->addFieldToFilter('form_id', $form->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {

        /** @var Forms $form */
        $form = $this->registry->registry('panda_form');

        $validation = false;
        foreach ($form->getElements() as $element) {
            if ($element->getData('email_validation')) {
                $validation = true;
                break;
            }
        }

        $this->addColumn(
            'entry_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'index'  => 'entry_id',
                'type'   => 'number',
            ]
        );

        if ($form->isFrontend() && $validation) {
            $this->addColumn(
                'validated',
                [
                    'header'  => __('Validated?'),
                    'align'   => 'right',
                    'index'   => 'validated',
                    'type'    => 'options',
                    'options' => [0 => __('No'), 1 => __('Yes')],
                ]
            );
        }

        $this->addColumn(
            'created_at',
            [
                'header'    => __('Created At'),
                'align'     => 'right',
                'type'      => 'datetime',
                'index'     => 'created_at',
                'gmtoffset' => true,
            ]
        );
        if ($form->isFrontend()) {
            $this->addColumn(
                'customer_id',
                [
                    'header'         => __('Customer'),
                    'align'          => 'center',
                    'filter'         => false,
                    'sortable'       => false,
                    'system'         => true,
                    'width'          => '75px',
                    'index'          => 'customer_id',
                    'frame_callback' => [$this, 'customerResult'],
                ]
            );

            $this->addColumn(
                'subscriber_id',
                [
                    'header'         => __('Subscriber ID'),
                    'align'          => 'center',
                    'width'          => '75px',
                    'index'          => 'subscriber_id',
                    'frame_callback' => [$this, 'subscriberResult'],
                ]
            );
        }

        /** @var \Licentia\Forms\Model\FormElements $element */
        foreach ($form->getElements() as $element) {
            $field = Forms::FIELD_IDENTIFIER . $element->getEntryCode();

            $fieldName = str_replace(Forms::FIELD_IDENTIFIER, 'field_', $field);

            if ($element->getType() == 'html') {
                continue;
            }

            if (!$element->getShowInGrid()) {
                continue;
            }

            $options = [
                'header' => __($element->getName()),
                'align'  => 'right',
                'index'  => $fieldName,
            ];

            if ($element->getType() == 'file' || $element->getType() == 'image') {
                $options['frame_callback'] = [$this, 'fileResult'];
            }

            if ($element->getType() == 'country') {
                $options['type'] = 'options';
                $options['options'] = Forms::getCountriesList();
            }

            if ($element->getType() == 'number') {
                $options['type'] = 'number';
                $options['filter_index'] = new \Zend_Db_Expr('CAST(`' . $fieldName . '` AS SIGNED)');
            }

            if ($element->getType() == 'date') {
                $options['type'] = 'date';
                $options['filter_index'] = new \Zend_Db_Expr('CAST(`' . $fieldName . '` AS DATE)');
            }

            $this->addColumn(
                $field,
                $options
            );
        }

        $this->addColumn(
            'entries',
            [
                'header'         => __('Entry'),
                'width'          => '50px',
                'frame_callback' => [$this, 'delete'],
                'filter'         => false,
                'sortable'       => false,
                'index'          => 'entry_id',
            ]
        );

        if ($form->isFrontend()) {
            $this->addColumn(
                'validate',
                [
                    'header'         => __('Entry'),
                    'width'          => '50px',
                    'frame_callback' => [$this, 'validate'],
                    'filter'         => false,
                    'sortable'       => false,
                    'index'          => 'validated',
                ]
            );
        }
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return bool
     */
    public function getRowUrl($row)
    {

        /** @var Forms $form */
        $form = $this->registry->registry('panda_form');

        if ($form->isFrontend()) {
            return false;
        }

        return $this->getUrl('*/*/newEntry', ['_current' => true, 'etid' => $row->getData('entry_id')]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function subscriberResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('panda/subscriber/edit', ['id' => $value]);

            return '<a href="' . $url . '">[' . $value . '] ' . __('View') . '</a>';
        }

        return __('No');
    }

    /**
     * @param $value
     *
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function delete($value, $row)
    {

        return '<a href="' . $this->getUrl(
                '*/*/deleteEntry',
                ['deid' => $value, 'id' => $row->getData('form_id')]
            ) . '" onclick="return confirm (\'' . __("Are you sure?") . '\')">' . __('Delete') . '</a>';
    }

    /**
     * @param $value
     *
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function validate($value, $row)
    {

        if ($value == 1) {
            return '<a href="' . $this->getUrl(
                    '*/*/ValidateEntry',
                    ['id' => $row->getData('entry_id'), 'validate' => 0]
                ) . '" onclick="return confirm (\'' . __("Are you sure?") . '\')">' . __('Void') . '</a>';
        } else {
            return '<a href="' . $this->getUrl(
                    '*/*/ValidateEntry',
                    ['id' => $row->getData('entry_id'), 'validate' => 1]
                ) . '" onclick="return confirm (\'' . __("Are you sure?") . '\')">' . __('Validate') . '</a>';
        }
    }

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('customer/index/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('View') . '</a>';
        }

        return __('No');
    }

    /**
     * @param $value
     * @param $row
     * @param $r
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function fileResult($value, $row, $r)
    {

        $nValue = json_decode($row->getData(str_replace('panda_', 'field_', $r->getId())), true);

        if (is_array($nValue)) {

            $return = '';
            foreach ($nValue as $key => $item) {
                $return .= '   |   <a target="_blank" href="' . $this->getUrl('*/*/view',
                        [
                            'viewid' => $row->getData('entry_id'),
                            'id'     => $row->getData('form_id'),
                            'field'  => str_replace('panda_', 'field_', $r->getId()),
                            'item'   => $key,
                        ]) . '">' . __('View') . '</a>';
            }

            return $return;
        }

        return __('N/A');
    }
}
