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

namespace Licentia\Forms\Block\Form;

use Licentia\Forms\Model\Forms;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Form Class
 */
class Form extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Licentia\Forms\Model\FormsFactory $formsFactory
     */
    protected $formsFactory;

    /**
     * @var Forms $form
     */
    protected $form = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Magento\Captcha\Block\Captcha
     */
    protected $captcha;

    /**
     * @var \Licentia\Forms\Model\FormElements
     */
    protected $formElementsFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Form constructor.
     *
     * @param \Licentia\Panda\Helper\Data                      $pandaHelper
     * @param DataPersistorInterface                           $dataPersistor
     * @param \Magento\Captcha\Block\Captcha                   $captcha
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Licentia\Forms\Model\FormElementsFactory        $formElementsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory         $formEntriesFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory         $extraFieldsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory         $subscribersFactory
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Licentia\Forms\Model\FormsFactory               $formsFactory
     * @param array                                            $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        DataPersistorInterface $dataPersistor,
        \Magento\Captcha\Block\Captcha $captcha,
        \Magento\Framework\View\Element\Template\Context $context,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->formsFactory = $formsFactory;
        $this->customerSession = $customerSession;
        $this->subscribersFactory = $subscribersFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->formEntriesFactory = $formEntriesFactory;
        $this->formElementsFactory = $formElementsFactory;
        $this->captcha = $captcha;
        $this->dataPersistor = $dataPersistor;
        $this->pandaHelper = $pandaHelper;

        $this->setTemplate('forms/form.phtml');
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {

        return null;
    }

    /**
     * @return bool
     */
    public function isEditing()
    {

        if ($this->customerSession->getCustomerId()) {
            $entriesCollection = $this->formEntriesFactory->create()->getCollection();

            $entriesCollection->addFieldToFilter('form_id', $this->getForm()->getId())
                              ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());

            if ($entriesCollection->getSize()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSubmitButton()
    {

        $title = $this->isEditing() ? $this->getForm()->getUpdateLabel() : $this->getForm()->getSubmitLabel();

        return ' <input type="hidden" name="hideit" id="hideit" value=""/>
                        <button id="panda-submit-form-' . $this->getForm()->getId() . '" type="submit"
                                title="' . __($this->getForm()->getSubmitLabel()) . '" class="action submit primary">
                        <span>' . __($title) . '</span>
                </button>';
    }

    /**
     * @return mixed|string
     */
    public function parseTemplate()
    {

        $form = $this->getForm();

        $template = $form->getTemplate();

        $template = str_replace(
            [
                '{title}',
                '{description}',
                '{button}',
            ],
            [
                $form->getTitle(),
                $form->getDescription(),
                $this->getSubmitButton(),
            ],
            $template
        );

        preg_match_all('/\{\{element_(\d{1,})+.*?\}\}/', $template, $vars);

        if (isset($vars[0][0])) {
            $i = 0;
            foreach ($vars[1] as $elementId) {

                /** @var  \Licentia\Forms\Model\FormElements $element */
                $element = $this->formElementsFactory->create()->load($elementId);

                if ($element->getFormId() == $form->getId()) {
                    preg_match('/hide="(.*?)"/', $vars[0][$i], $parts);

                    if (isset($parts[1])) {
                        $parts = $parts[1];
                    } else {
                        $parts = null;
                    }

                    $template = str_replace($vars[0][$i], $this->getElementHtml($element, $parts), $template);

                    $i++;
                }
            }
        }

        return $template;
    }

    /**
     * @return bool|int
     */
    public function canUserSubmit()
    {

        if (!$this->isEnabled()) {
            return false;
        }

        if ($this->getForm()->getRegisteredOnly() == 0) {
            return true;
        }

        if ($this->getForm()->getRegisteredOnly() == 1 && !$this->customerSession->isLoggedIn()) {
            return 1;
        }

        if ($this->getForm()->getRegisteredOnly() == 2 && !$this->customerSession->isLoggedIn()) {
            return 2;
        }

        if ($this->getForm()->getRegisteredOnly() && $this->customerSession->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {

        return $this->getForm()->isEnabled();
    }

    /**
     * @param null $formId
     *
     * @return Forms
     */
    public function getForm($formId = null)
    {

        if (null === $this->form || $formId !== null) {
            $this->form = $this->formsFactory->create()->load($formId);
        }

        if ($formId = $this->getRequest()->getParam('id')) {
            $this->form = $this->formsFactory->create()->load($formId);
        }

        if (!$this->form->getId()) {
            $this->form = $this->formsFactory->create()->load($this->getData('form_id'));
        }

        return $this->form;
    }

    /**
     * @return string
     */
    public function getCaptchaElement()
    {

        $data = [];
        $data['type'] = 'Magento\Captcha\Block\Captcha';
        $data['form_id'] = 'panda_forms';
        $data['img_width'] = '230';
        $data['img_height'] = '50';
        $data['module_name'] = 'Magento_Captcha';

        return $this->captcha->setData($data)
                             ->toHtml();
    }

    /**
     * @param \Licentia\Forms\Model\FormElements $element
     * @param null                               $excludeParts
     *
     * @return string
     */
    public function getElementHtml(\Licentia\Forms\Model\FormElements $element, $excludeParts = null)
    {

        $defaultParts = ['label', 'hint'];

        if ($excludeParts === null) {
            $parts = $defaultParts;
        } else {
            $excludeParts = explode(',', $excludeParts);
            $excludeParts = array_filter($excludeParts);
            $parts = array_diff($defaultParts, $excludeParts);
        }

        $wasCountry = false;
        if ($element->getType() == 'country') {
            $wasCountry = true;
            $element->setType('select');
            $element->setOptions(Forms::getCountriesList());
        }
        if ($element->getType() == 'rating') {
            $element->setOptions(
                array_combine(range((int) $element->getStars(), 1), range((int) $element->getStars(), 1))
            );
        }

        $names = [$element->getEntryCode()];

        if ($element->getOptions()) {
            if (!is_array($element->getOptions())) {
                $names = str_getcsv($element->getOptions());
                $names = array_filter($names);
            }
            if (is_array($element->getOptions())) {
                $names = $element->getOptions();
            }
        }

        $canEdit = $this->getForm($element->getFormId())
                        ->getCanEdit();

        if (($canEdit || $this->getForm()->getManageSubscription()) &&
            $this->customerSession->isLoggedIn()) {
            $entry = $this->formEntriesFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())
                ->addFieldToFilter('form_id', $this->getForm()->getId())
                ->setOrder('entry_id', 'DESC')
                ->setPageSize(1)
                ->getFirstItem();

            if ($entry->getData('field_' . $element->getEntryCode())) {
                $element->setDefault($entry->getData('field_' . $element->getEntryCode()));
            }
        }

        if ($this->getData('subscriber')) {
            $entry = $this->formEntriesFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('subscriber_id', $this->getData('subscriber')->getId())
                ->addFieldToFilter('form_id', $this->getForm()->getId())
                ->setOrder('entry_id', 'DESC')
                ->setPageSize(1)
                ->getFirstItem();

            if ($entry->getData('field_' . $element->getEntryCode())) {
                $element->setDefault($entry->getData('field_' . $element->getEntryCode()));
            }
        }

        $data = $this->dataPersistor->get('form_data_' . $element->getFormId());

        if (isset($data[Forms::FIELD_IDENTIFIER . $element->getEntryCode()])) {
            $savedValue = $data[Forms::FIELD_IDENTIFIER . $element->getEntryCode()];

            if (is_array($savedValue) && !in_array($element->getType(), ['file', 'image', 'captcha'])) {
                $savedValue = implode(',', $savedValue);
            }

            if (is_string($savedValue)) {
                $element->setDefault($savedValue);
            }
        }

        $elementDecoratorStart = '';
        $elementDecoratorEnd = '';

        $names = array_combine($names, $names);

        if ($element->getMap() && in_array($element->getType(), ['radios', 'checkboxes', 'select'])) {
            $optionsExtraField = $this->extraFieldsFactory->create()
                                                          ->load($element->getMap(), 'entry_code');

            if ($optionsExtraField->getOptions()) {
                $names = str_getcsv($optionsExtraField->getOptions());
                $names = array_combine($names, $names);

                if (!$element->getDefault()) {
                    $element->setDefault($optionsExtraField->getDefaultValue());
                }

                if ($element->getMap()) {
                    $optionsExtraField = $this->extraFieldsFactory->create()
                                                                  ->load($element->getMap(), 'entry_code');

                    if ($optionsExtraField->getOptions()) {
                        $names = str_getcsv($optionsExtraField->getOptions());
                        $names = array_combine($names, $names);
                    }

                    if (!$element->getDefault()) {
                        $element->setDefault($optionsExtraField->getDefaultValue());
                    }
                }
                if ($element->getRequired() == 'select') {
                    $names = ['' => __('-- Choose One --')] + $names;
                }
            }
        }

        if ($element->getType() == 'select') {
            $elementDecoratorStart = '<select ';
            $elementDecoratorEnd = '</select>';
        }

        $html = '';
        $attrs = [];
        foreach ($names as $index => $label) {
            $optionStart = '';
            $optionEnd = '';

            $label = trim($label);
            $index = trim($index);

            $attrs = [];

            $name = Forms::FIELD_IDENTIFIER . $element->getEntryCode();
            $id = $name;
            if (count($names) > 1 && $element->getType() != 'select') {
                $name = Forms::FIELD_IDENTIFIER . $element->getEntryCode() . '[]';

                $id = Forms::FIELD_IDENTIFIER . $element->getEntryCode() . $label;

                $attrs['value'] = $label;

                if (count($names) > 1) {
                    if (is_array($element->getDefault()) && in_array($label, $element->getDefault())) {
                        $element->setChecked(1);
                    }
                }
            }

            if ($element->getType() == 'email' && $this->customerSession->isLoggedIn()) {
                $element->setDefault(
                    $this->customerSession->getCustomer()
                                          ->getEmail()
                );
            }

            if ($element->getType() == 'phone' && $this->customerSession->isLoggedIn()) {
                $customer = $this->customerSession->getCustomer();

                $subscriber = $this->subscribersFactory->create()->load($customer->getId());

                if ($subscriber->getId() && $subscriber->getCellphone()) {
                    $element->setDefault($subscriber->getCellphone());
                } else {
                    if ($customer->getDefaultBillingAddress() &&
                        $number = $customer->getDefaultBillingAddress()
                                           ->getTelephone()
                    ) {
                        $prefix = \Licentia\Panda\Helper\Data::getPrefixForCountry(
                            $customer->getDefaultBillingAddress()
                                     ->getCountryId()
                        );

                        $number = $prefix . '-' . ltrim(preg_replace('/\D/', '', $number), $prefix);

                        $element->setDefault($number);
                    } elseif ($customer->getDefaultShippingAddress() &&
                              $number = $customer->getDefaultShippingAddress()
                                                 ->getTelephone()
                    ) {
                        $prefix = \Licentia\Panda\Helper\Data::getPrefixForCountry(
                            $customer->getDefaultShippingAddress()
                                     ->getCountryId()
                        );

                        $number = $prefix . '-' . ltrim(preg_replace('/\D/', '', $number), $prefix);

                        $element->setDefault($number);
                    }
                }
            }

            if ($element->getType() == 'phone' && !$element->getDefault()) {
                $code = \Licentia\Panda\Helper\Data::getPrefixForCountry($this->pandaHelper->getCountryCode());

                if ($code) {
                    $element->setDefault($code . '-');
                }
            }

            if ($wasCountry && !$element->getDefault()) {
                $element->setDefault($this->pandaHelper->getCountryName());
            }

            if ($element->getType() == 'hidden') {
                $params = explode(',', $element->getParams());

                foreach ($params as $param) {
                    if ($this->getRequest()->getParam($param)) {
                        $element->setDefault((string) $this->getRequest()->getParam($param));
                    }
                }
            }

            $name = trim($name);
            $id = trim($id);

            $attrs['name'] = $name;
            $attrs['id'] = $id;

            $elementStart = "<input ";
            $elementEnd = "/>";

            switch ($element->getType()) {
                case 'select':
                    $attrs['type'] = 'select';
                    $elementStart = "<option ";
                    $elementEnd = "</option>";
                    break;
                case 'text':
                    $attrs['type'] = 'text';
                    break;
                case 'file':
                case 'image':
                    $attrs['type'] = 'file';
                    $attrs['name'] = $name . '[]';

                    if ($element->getAllowMultiple() > 1) {
                        $attrs['multiple'] = 'multiple';
                    }

                    $elementDecoratorStart = '';

                    if ($element->getDefault() && $element->getAllowMultiple() <= 1 && !$element->getProtected()) {
                        $decoded = json_decode($element->getDefault(), true);

                        if (is_array($decoded)) {
                            $element->setDefault($decoded[0]);
                        }

                        $mediaDir = $this->_storeManager->getStore()
                                                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                        $newDir = $mediaDir . $element->getDefault();

                        $file = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                                  ->getAbsolutePath($element->getDefault());

                        if (is_file($file)) {
                            $info = pathinfo($file, PATHINFO_EXTENSION);
                            if ($element->getType() == 'image' &&
                                in_array(strtolower($info), ['png', 'jpg', 'gif', 'jpeg'])) {
                                $elementDecoratorEnd = '<div><img style="overflow:hidden; max-width:500px; ' .
                                                       'max-height:220px; margin:10px; " src="' . $newDir . '" />' .
                                                       '</div>';
                            } else {
                                $elementDecoratorEnd = '<div><a target="_blank" href="' . $newDir . '">' .
                                                       __('View Uploaded') . '</a></div>';
                            }
                        }
                    }
                    break;
                case 'hidden':
                    $attrs['type'] = 'hidden';
                    break;
                case 'date':
                    $format = $this->_localeDate->getDateFormat();
                    $attrs['type'] = 'text';
                    $attrs['style'] = 'width:150px;';
                    $optionStart = '<div>';
                    $optionEnd = '</div> 
                                <script type="text/javascript">
                                    require(["jquery", "mage/calendar"], function ($) {
                                        $("#' . $id . '").calendar({
                                            showsTime: false,
                                            dateFormat: \'' . $format . '\'
                                        })
                                    });
                                </script>';
                    break;
                case 'email':
                    $attrs['type'] = 'email';
                    $attrs['data-validate']['validate-email'] = true;
                    break;
                case 'phone':
                    $attrs['type'] = 'tel';
                    /*
                    $attrs['data-validate']['remote'] = $this->getUrl(
                        'panda/form/validate',
                        [
                            'form_id' => $element->getFormId(),
                            '_secure' => true,
                            'type'    => 'phone',
                        ]
                    );*/
                    break;
                case 'url':
                    $attrs['type'] = 'text';
                    $attrs['data-validate']['remote'] = $this->getUrl(
                        'pandaf/form/validate',
                        [
                            'form_id' => $element->getFormId(),
                            '_secure' => true,
                            'type'    => 'url',
                        ]
                    );
                    break;
                case 'number':
                    $attrs['type'] = 'number';
                    $attrs['data-validate']['number'] = true;
                    break;
                case 'checkbox':
                case 'checkboxes':
                    $attrs['type'] = 'checkbox';
                    $attrs['value'] = 'checked';
                    $optionStart = '<div>';
                    $optionEnd = '</div>';
                    break;
                case 'rating':
                    $attrs['type'] = 'radio';
                    break;
                case 'captcha':
                    $attrs['type'] = 'captcha';
                    break;
                case 'radios':
                    $attrs['type'] = 'radio';
                    $optionStart = '<div>';
                    $optionEnd = '</div>';
                    break;
                case 'textarea':
                    $elementStart = "<textarea ";
                    $elementEnd = "/>{$this->escapeHtml($element->getDefault())}</textarea>";
                    break;
            }

            if ($element->getPlaceholder()) {
                $attrs['placeholder'] = $element->getPlaceholder();
            }

            if ($element->getDefault()) {
                $attrs['value'] = $element->getDefault();
            }

            if ($element->getDisabled()) {
                $attrs['disabled'] = 'disabled';
            }

            if ($element->getCssClass()) {
                $attrs['class'] = $element->getCssClass();
            }

            if ($element->getChecked()) {
                $attrs['checked'] = 'checked';
            }

            if ($element->getMinNumber()) {
                $attrs['min'] = $element->getMinNumber();
            }

            if ($element->getMaxNumber()) {
                $attrs['max'] = $element->getMaxNumber();
            }

            if ($element->getMaxLength()) {
                if (!isset($attrs['class'])) {
                    $attrs['class'] = '';
                }
                $attrs['data-validate']['validate-length'] = true;
                $attrs['class'] .= ' validate-length  maximum-length-' . $element->getMaxLength() . ' ';
            }

            if ($element->getMinLength()) {
                if (!isset($attrs['class'])) {
                    $attrs['class'] = '';
                }
                $attrs['data-validate']['validate-length'] = true;
                $attrs['class'] .= ' validate-length  minimum-length-' . $element->getMinLength() . ' ';
            }

            if ($element->getPattern()) {
                $attrs['pattern'] = $element->getPattern();
            }

            if ($element->getRequired()) {
                $attrs['data-validate']['required'] = true;
            }

            if (($element->getType() == 'file' || $element->getType() == 'image') &&
                $this->isEditing() && $element->getDefault()) {
                unset($attrs['data-validate']);
            }

            if ($element->getUnique()) {
                $attrs['data-validate']['remote'] = $this->getUrl(
                    'pandaf/form/validate',
                    [
                        'form_id' => $element->getFormId(),
                        '_secure' => true,
                        'type'    => 'unique',
                    ]
                );
            }

            if (isset($attrs['data-validate'])) {
                $attrs['data-validate'] = \Zend_Json_Encoder::encode($attrs['data-validate']);
            }

            if ($element->getType() == 'checkboxes' ||
                $element->getType() == 'radios' ||
                $element->getType() == 'rating') {
                $attrs['value'] = $label;

                $checked = str_getcsv($element->getDefault());
                if (in_array($label, $checked)) {
                    $attrs['checked'] = 'checked';
                }
            }
            if ($element->getType() == 'checkbox') {
                if ($element->getDefault() == 'checked') {
                    $attrs['checked'] = 'checked';
                }
            }

            $html .= $optionStart;
            $html .= $elementStart;

            $attrsBuilt = '';
            foreach ($attrs as $key => $value) {
                if (is_array($value)) {
                    continue;
                }

                $attrsBuilt .= $this->escapeHtmlAttr($key) . "='{$this->escapeHtmlAttr($value)}'";
            }

            if ($element->getType() == 'select') {
                $selected = '';
                if (trim($label) == trim($element->getDefault())) {
                    $selected = 'selected ="selected" ';
                }

                $attrsBuilt = "value='{$this->escapeHtmlAttr($index)}' " . $selected . " >" . __($label);
                $html .= $attrsBuilt;
            } else {
                $html .= $attrsBuilt;
            }

            $html .= $elementEnd;

            if ($element->getType() == 'checkboxes' ||
                $element->getType() == 'radios' ||
                $element->getType() == 'rating') {
                $html .= "<label for='{$this->escapeHtmlAttr($attrs['id'])}'>" . __($label) . '</label>';
            }

            if ($element->getType() == 'checkbox' && in_array('hint', $parts)) {
                $html .= "<label for='{$this->escapeHtmlAttr($attrs['id'])}'>" . $element->getHint() . '</label>';
            }

            if ($element->getType() == 'html') {
                $html = $element->getHtml();
            }

            if ($element->getType() == 'captcha') {
                $html = $this->getCaptchaElement();
            }

            $html .= $optionEnd;
        }

        if ($element->getType() == 'select') {
            $attrsBuilt = '';

            unset($attrs['value'], $attrs['type']);

            foreach ($attrs as $key => $value) {
                $attrsBuilt .= ' ' . $this->escapeHtmlAttr($key) . "='{$this->escapeHtmlAttr($value)}' ";
            }

            $elementDecoratorStart = $elementDecoratorStart . $attrsBuilt . '>';
        }

        $html = $elementDecoratorStart . $html . $elementDecoratorEnd;

        if ($element->getType() != 'html' &&
            $element->getType() != 'hidden' &&
            in_array('label', $parts)
        ) {
            $html = " <div class='label field'><label for='{$this->escapeHtmlAttr($element->getIdAttribute())}'>
             <span>" . __($element->getName()) . '</span></label></div>' . $html;
        }

        if ($element->getType() != 'html' &&
            $element->getType() != 'hidden' &&
            $element->getType() != 'checkbox' &&
            in_array('hint', $parts)
        ) {
            $html = $html . '<em>' . $element->getHint() . '</em>';
        }

        if ($element->getType() != 'hidden' && in_array('label', $parts)) {
            $required = ($element->getRequired() == 1) ? "required" : "";
            $extraLabel = '';

            if ($this->isEditing() &&
                ($element->getType() == 'file' || $element->getType() == 'image') &&
                !$element->getRequired() &&
                $element->getDefault() &&
                (int) $element->getAllowMultiple() <= 1
            ) {
                $deleteName = Forms::FIELD_IDENTIFIER . $element->getEntryCode() . '_delete';

                $extraLabel = '<input id="' . $deleteName . '" type="checkbox" value="1" name="' . $deleteName . '">'
                              . '<label for="' . $deleteName . '">' . __('Delete file') . '</label>';
            }

            if (($element->getType() == 'file' &&
                 $element->getType() == 'image') &&
                $this->isEditing() &&
                $element->getDefault()) {
                $required = '';
            }

            $html = '<div class="field ' . $element->getCssClass() . '  ' . $element->getType() . '  ' .
                    $element->getName() . ' ' . $required . ' ">' . $html . $extraLabel . '</div>';
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {

        $this->setData('form', $this->getForm());

        return parent::_toHtml();
    }
}
