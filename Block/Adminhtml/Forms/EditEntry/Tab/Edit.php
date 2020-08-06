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

namespace Licentia\Forms\Block\Adminhtml\Forms\EditEntry\Tab;

/**
 * Class Elements
 *
 * @package Licentia\Forms\Block\Adminhtml\Forms\EditEntry\Tab
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
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Licentia\Forms\Model\FormElementsFactory
     */
    protected $formElementsFactory;

    /**
     * @var \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory
     */
    protected $formElementsCollection;

    /**
     * Edit constructor.
     *
     * @param \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Block\Template\Context                            $context
     * @param \Magento\Framework\Registry                                        $registry
     * @param \Magento\Framework\Data\FormFactory                                $formFactory
     * @param \Licentia\Panda\Helper\Data                                        $pandaHelper
     * @param \Magento\Store\Model\System\Store                                  $systemStore
     * @param \Licentia\Forms\Model\FormsFactory                                 $formsFactory
     * @param \Licentia\Forms\Model\FormElementsFactory                          $formElementsFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config                                  $wysiwygConfig
     * @param array                                                              $data
     */
    public function __construct(
        \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $collectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->formsFactory = $formsFactory;
        $this->systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->formElementsFactory = $formElementsFactory;
        $this->formElementsCollection = $collectionFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Forms\Model\Forms $currentForm */
        $currentForm = $this->_coreRegistry->registry('panda_form');

        /** @var \Licentia\Forms\Model\FormEntries $currentForm */
        $currentEntry = $this->_coreRegistry->registry('panda_form_entry');

        if (!$currentEntry->getData('store_ids')) {
            $currentEntry->setData('store_ids', '0');
        }
        $currentEntry->setData("store_ids", explode(',', $currentEntry->getData('store_ids')));

        /** @var \Licentia\Forms\Model\ResourceModel\FormElements\Collection $elements */
        $elements = $currentForm->getActiveElements();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => 'saveEntry',
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'content_fieldset',
            ['legend' => __('Add entry to %1', $currentForm->getName())]
        );

        if ($currentEntry->getId()) {
            $fieldset->addField(
                'entry_id',
                'hidden',
                [
                    'name'  => 'entry_id',
                    'value' => $currentEntry->getId(),
                ]
            );
        }
        $wysiwygConfig = $this->wysiwygConfig->getConfig(
            ['tab_id' => $this->getTabId()]
        );
        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset->addField(
            'validated',
            'select',
            [
                'name'    => 'validated',
                'label'   => __('Active'),
                'title'   => __('Active'),
                'options' => [0 => __('No'), 1 => __('Yes')],
            ]
        );

        $options = $this->systemStore->getStoreValuesForForm();
        array_unshift($options, ['label' => __('-- Any --'), 'value' => 0]);
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'     => 'store_ids',
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'required' => true,
                'values'   => $options,
            ]
        );

        /** @var \Licentia\Forms\Model\FormElements $element */
        foreach ($elements as $element) {
            $name = \Licentia\Forms\Model\Forms::FIELD_IDENTIFIER . $element->getEntryCode();

            $nameInput = $name;

            if (($element->getType() == 'file' || $element->getType() == 'image') && $element->getExtensions()) {
                $element->setHint($element->getHint() . ' Allowed extensions: ' . $element->getExtensions());

                $nameInput = $name . '[]';
            }

            if (in_array($element->getType(), ['file', 'image'])) {
                $newValue = json_decode($currentEntry->getData('field_' . $element->getEntryCode()), true);

                if (is_array($newValue) && isset($newValue[0])) {
                    $currentEntry->setData('field_' . $element->getEntryCode(), $newValue[0]);
                } else {
                    $currentEntry->setData('field_' . $element->getEntryCode(), '');
                }
            }

            $fieldset->addField(
                $name,
                $this->getInputTypeForFormElement($element),
                [
                    'name'        => $nameInput,
                    'label'       => $element->getName(),
                    'title'       => $element->getName(),
                    'required'    => $element->getRequired(),
                    'note'        => __($element->getHint()),
                    'values'      => $this->getInputValuesForFormElement($element),
                    'class'       => $this->getCssClassesForFormElement($element),
                    'date_format' => $dateFormat,
                    'disabled'    => $element->getDisabled() ? true : false,
                    'config'      => $wysiwygConfig,
                ]
            );
        }

        $this->setForm($form);

        if ($currentEntry) {
            $elementsData = $currentEntry->getData();

            foreach ($elementsData as $key => $value) {
                if (is_array($value)) {
                    continue;
                }

                $elementsData[str_replace('field_', 'panda_', $key)] = $value;
            }

            $form->addValues($elementsData);
        }

        return parent::_prepareForm();
    }

    /**
     * @param \Licentia\Forms\Model\FormElements $element
     *
     * @return string
     */
    public function getCssClassesForFormElement(\Licentia\Forms\Model\FormElements $element)
    {

        $class = '';

        switch ($element->getType()) {
            case 'radios':
                return 'small_input';
                break;
            case 'email':
                return 'validate-email';
                break;
            case 'url':
                return 'validate-url';
                break;
            case 'number':
                $class = 'validate-number ';
                if ($element->getMaxNumber()) {
                    $class .= ' validate-number-range number-range-' . $element->getMinNumber() .
                              '-' . $element->getMaxNumber();
                }
                break;
        }

        return $class;
    }

    /**
     * @param \Licentia\Forms\Model\FormElements $element
     *
     * @return string
     */
    public function getInputTypeForFormElement(\Licentia\Forms\Model\FormElements $element)
    {

        switch ($element->getType()) {
            case 'checkboxes':
            case 'radios':
                return 'multiselect';
                break;
            case 'checkbox':
            case 'country':
            case 'rating':
            case 'select':
                return 'select';
                break;
            case 'textarea':
                return 'editor';
                break;
            case 'file':
                return 'file';
                break;
            case 'image':
                return 'image';
                break;
            case 'date':
                return 'date';
                break;
        }

        return 'text';
    }

    /**
     * @param \Licentia\Forms\Model\FormElements $element
     *
     * @return array
     */
    public function getInputValuesForFormElement(\Licentia\Forms\Model\FormElements $element)
    {

        $return = null;
        if ($element->getType() == 'checkbox') {
            $element->setOptions('Yes');
        }

        $values = str_getcsv($element->getOptions());

        if ($values) {
            $return = [];
            foreach ($values as $value) {
                $return[] = ['value' => $value, 'label' => $value];
            }
        }

        switch ($element->getType()) {
            case 'country':
                return \Licentia\Forms\Model\Forms::getCountriesList();
                break;
            case 'rating':
                return array_combine(range(1, $element->getStars()), range(1, $element->getStars()));
                break;
            case 'radios':
            case 'checkboxes':
            case 'checkbox':
            case 'select':
                return $return;
                break;
        }

        return null;
    }
}
