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

namespace Licentia\Forms\Model;

use Licentia\Forms\Api\Data\FormsInterface;

/**
 * Class Forms
 *
 * @package Licentia\Forms\Model
 */
class Forms extends \Magento\Framework\Model\AbstractModel
    implements \Magento\Framework\Option\ArrayInterface, FormsInterface
{

    /**
     *
     */
    const FIELD_IDENTIFIER = 'panda_';

    /**
     *
     */
    const FORMS_MAX_NUMBER_FIELDS = 25;

    /**
     *
     */
    const ELEMENTS_TYPES = [
        'captcha'    => 'CAPTCHA',
        'checkbox'   => 'Checkbox (One)',
        'checkboxes' => 'Checkboxes (Multiple)',
        'country'    => 'Country List',
        'date'       => 'Date',
        'email'      => 'Email',
        'file'       => 'File',
        'hidden'     => 'Hidden Field',
        'html'       => 'HTML Text',
        'image'      => 'Image',
        'textarea'   => 'Long Text',
        'number'     => 'Number',
        'phone'      => 'Phone Number',
        'rating'     => 'Rating',
        'radios'     => 'Radios (Multiple)',
        'select'     => 'Select List',
        'text'       => 'Text',
        'url'        => 'URL',
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_forms';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'forms';

    /**
     * @var \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory
     */
    protected $elementsCollection;

    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
     */
    protected static $countryInformationAcquirer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Forms constructor.
     *
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date               $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param ResourceModel\FormElements\CollectionFactory                 $elementsCollection
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterface   $countryInformationAcquirer
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory $elementsCollection,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        self::$countryInformationAcquirer = $countryInformationAcquirer;

        $this->dateFilter = $dateFilter;
        $this->elementsCollection = $elementsCollection;
        $this->timezone = $timezone;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Forms::class);
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBeforeSave()
    {

        $date = $this->pandaHelper->gmtDate('Y-m-d');

        if ($this->getData('from_date') && $this->getData('to_date')) {
            try {
                $inputFilter = new \Zend_Filter_Input(
                    ['to_date' => $this->dateFilter, 'from_date' => $this->dateFilter],
                    [],
                    $this->getData()
                );
                $data = $inputFilter->getUnescaped();
                $this->addData($data);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date format'));
            }

            try {
                $this->timezone->formatDate($this->getData('from_date'));
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in From Date'));
            }

            try {
                $this->timezone->formatDate($this->getData('to_date'));
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in To Date'));
            }

            if ($this->getData('from_date') > $this->getData('to_date')) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than start date')
                );
            }

            if ($this->getData('to_date') < $date) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than today')
                );
            }
        }

        if ($this->getCode() && !$this->getOrigData('code')) {
            $unique = $this->getCollection()->addFieldToFilter('code', $this->getCode());

            if ($unique->count() > 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Duplicated value for the field: Code'));
            }
        }

        return parent::validateBeforeSave();
    }

    /**
     * @param null $storeId
     *
     * @return bool|\Magento\Framework\DataObject
     */
    public function getFormForManagePage($storeId = null)
    {

        $collection = $this->getCollection()
                           ->addFieldToFilter('manage_subscription', 1)
                           ->addFieldToFilter('is_active', 1)
                           ->setPageSize(1);

        if ($storeId) {
            $collection->getSelect()->where('FIND_IN_SET(?,store_id) OR store_id IS NULL OR store_id=0', $storeId);
        }

        if ($collection->getSize() == 1 && $collection->getFirstItem()->isEnabled()) {
            return $collection->getFirstItem();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {

        $date = $this->pandaHelper->gmtDate();

        if ($this->getData('max_entries') > 0 &&
            $this->getData('max_entries') <= $this->getData('entries')
        ) {
            return false;
        }

        if ($this->getData('is_active') != 1) {
            return false;
        }

        if ($this->getData('from_date') > $date) {
            return false;
        }

        if ($this->getData('to_date') < $date) {
            return false;
        }

        if ($this->getActiveElements()->getSize() == 0) {
            return false;
        }

        return true;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getActiveElements()
    {

        return $this->getElements()
                    ->addFieldToFilter('is_active', 1);
    }

    /**
     * @return ResourceModel\FormElements\Collection
     */
    public function getElements()
    {

        return $this->elementsCollection->create()
                                        ->setOrder('sort_order', 'ASC')
                                        ->addFieldToFilter('form_id', $this->getId());
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getEmailField()
    {

        return $this->getElements()
                    ->addFieldToFilter('type', 'email')
                    ->addFieldToFilter('map', 'email')
                    ->getFirstItem();
    }

    /**
     * @return array
     */
    public static function getCountriesList()
    {

        $countries = self::$countryInformationAcquirer->getCountriesInfo();
        $countryList = [];
        foreach ($countries as $country) {
            $countryList[$country->getFullNameLocale()] = $country->getFullNameLocale();
        }

        asort($countryList);

        return $countryList;
    }

    /**
     * @return array
     */
    public function toFormValues()
    {

        $values = $this->toOptionArray();

        $return = [];
        foreach ($values as $rule) {
            $return[$rule['value']] = $rule['label'];
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function isFrontend()
    {

        return $this->getEntryType() == 'frontend' ? true : false;
    }

    /**
     * @param bool $first
     *
     * @return array
     */
    public function toOptionArray($first = false)
    {

        $collection = $this->getCollection()->addFieldToFilter('entry_type', 'frontend');

        $return = [];

        if ($first) {
            $return[] = ['value' => '0', 'label' => $first];
        }

        foreach ($collection as $item) {
            $return[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $return;
    }

    /**
     * Get form_id
     *
     * @return string
     */
    public function getFormId()
    {

        return $this->getData(self::FORM_ID);
    }

    /**
     * Set form_id
     *
     * @param string $form_id
     *
     * @return FormsInterface
     */
    public function setFormId($form_id)
    {

        return $this->setData(self::FORM_ID, $form_id);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {

        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FormsInterface
     */
    public function setName($name)
    {

        return $this->setData(self::NAME, $name);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return FormsInterface
     */
    public function setTitle($title)
    {

        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return FormsInterface
     */
    public function setDescription($description)
    {

        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {

        return $this->getData(self::ACTIVE);
    }

    /**
     * Set active
     *
     * @param string $active
     *
     * @return FormsInterface
     */
    public function setActive($active)
    {

        return $this->setData(self::ACTIVE, $active);
    }

    /**
     * Get can_edit
     *
     * @return string
     */
    public function getCanEdit()
    {

        return $this->getData(self::CAN_EDIT);
    }

    /**
     * Set can_edit
     *
     * @param string $can_edit
     *
     * @return FormsInterface
     */
    public function setCanEdit($can_edit)
    {

        return $this->setData(self::CAN_EDIT, $can_edit);
    }

    /**
     * Get from_date
     *
     * @return string
     */
    public function getFromDate()
    {

        return $this->getData(self::FROM_DATE);
    }

    /**
     * Set from_date
     *
     * @param string $from_date
     *
     * @return FormsInterface
     */
    public function setFromDate($from_date)
    {

        return $this->setData(self::FROM_DATE, $from_date);
    }

    /**
     * Get to_date
     *
     * @return string
     */
    public function getToDate()
    {

        return $this->getData(self::TO_DATE);
    }

    /**
     * Set to_date
     *
     * @param string $to_date
     *
     * @return FormsInterface
     */
    public function setToDate($to_date)
    {

        return $this->setData(self::TO_DATE, $to_date);
    }

    /**
     * Get registered_only
     *
     * @return string
     */
    public function getRegisteredOnly()
    {

        return $this->getData(self::REGISTERED_ONLY);
    }

    /**
     * Set registered_only
     *
     * @param string $registered_only
     *
     * @return FormsInterface
     */
    public function setRegisteredOnly($registered_only)
    {

        return $this->setData(self::REGISTERED_ONLY, $registered_only);
    }

    /**
     * Get entries
     *
     * @return string
     */
    public function getEntries()
    {

        return $this->getData(self::ENTRIES);
    }

    /**
     * Set entries
     *
     * @param string $entries
     *
     * @return FormsInterface
     */
    public function setEntries($entries)
    {

        return $this->setData(self::ENTRIES, $entries);
    }

    /**
     * Get subscribers
     *
     * @return string
     */
    public function getSubscribers()
    {

        return $this->getData(self::SUBSCRIBERS);
    }

    /**
     * Set subscribers
     *
     * @param string $subscribers
     *
     * @return FormsInterface
     */
    public function setSubscribers($subscribers)
    {

        return $this->setData(self::SUBSCRIBERS, $subscribers);
    }

    /**
     * Get max_entries
     *
     * @return string
     */
    public function getMaxEntries()
    {

        return $this->getData(self::MAX_ENTRIES);
    }

    /**
     * Set max_entries
     *
     * @param string $max_entries
     *
     * @return FormsInterface
     */
    public function setMaxEntries($max_entries)
    {

        return $this->setData(self::MAX_ENTRIES, $max_entries);
    }

    /**
     * Get submit_label
     *
     * @return string
     */
    public function getSubmitLabel()
    {

        return $this->getData(self::SUBMIT_LABEL);
    }

    /**
     * Set submit_label
     *
     * @param string $submit_label
     *
     * @return FormsInterface
     */
    public function setSubmitLabel($submit_label)
    {

        return $this->setData(self::SUBMIT_LABEL, $submit_label);
    }

    /**
     * Get update_label
     *
     * @return string
     */
    public function getUpdateLabel()
    {

        return $this->getData(self::UPDATE_LABEL);
    }

    /**
     * Set update_label
     *
     * @param string $update_label
     *
     * @return FormsInterface
     */
    public function setUpdateLabel($update_label)
    {

        return $this->setData(self::UPDATE_LABEL, $update_label);
    }

    /**
     * Get css_class
     *
     * @return string
     */
    public function getCssClass()
    {

        return $this->getData(self::CSS_CLASS);
    }

    /**
     * Set css_class
     *
     * @param string $css_class
     *
     * @return FormsInterface
     */
    public function setCssClass($css_class)
    {

        return $this->setData(self::CSS_CLASS, $css_class);
    }

    /**
     * Get success_page
     *
     * @return string
     */
    public function getSuccessPage()
    {

        return $this->getData(self::SUCCESS_PAGE);
    }

    /**
     * Set success_page
     *
     * @param string $success_page
     *
     * @return FormsInterface
     */
    public function setSuccessPage($success_page)
    {

        return $this->setData(self::SUCCESS_PAGE, $success_page);
    }

    /**
     * Get success_message
     *
     * @return string
     */
    public function getSuccessMessage()
    {

        return $this->getData(self::SUCCESS_MESSAGE);
    }

    /**
     * Set success_message
     *
     * @param string $success_message
     *
     * @return FormsInterface
     */
    public function setSuccessMessage($success_message)
    {

        return $this->setData(self::SUCCESS_MESSAGE, $success_message);
    }

    /**
     * Get success_message
     *
     * @return string
     */
    public function getTemplate()
    {

        return $this->getData(self::TEMPLATE);
    }

    /**
     * Set success_message
     *
     * @param string $template
     *
     * @return FormsInterface
     */
    public function setTemplate($template)
    {

        return $this->setData(self::TEMPLATE, $template);
    }

    /**
     * Get enbled_template
     *
     * @return string
     */
    public function getEnableTemplate()
    {

        return $this->getData(self::ENABLE_TEMPLATE);
    }

    /**
     * Set success_message
     *
     * @param string $template
     *
     * @return FormsInterface
     */
    public function setEnableTemplate($template)
    {

        return $this->setData(self::ENABLE_TEMPLATE, $template);
    }

    /**
     * Get manage_subscription
     *
     * @return string
     */
    public function getManageSubscription()
    {

        return $this->getData(self::MANAGE_SUBSCRIPTION);
    }

    /**
     * Set manage_subscription
     *
     * @param string $subscription
     *
     * @return FormsInterface
     */
    public function setManageSubscription($subscription)
    {

        return $this->setData(self::MANAGE_SUBSCRIPTION, $subscription);
    }

    /**
     * Get entry_type
     *
     * @return string
     */
    public function getEntryType()
    {

        return $this->getData(self::ENTRY_TYPE);
    }

    /**
     * Set entry_type
     *
     * @param string $entryType
     *
     * @return FormsInterface
     */
    public function setEntryType($entryType)
    {

        return $this->setData(self::ENTRY_TYPE, $entryType);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {

        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return FormsInterface
     */
    public function setCode($code)
    {

        return $this->setData(self::CODE, $code);
    }

    /**
     * Get notifications
     *
     * @return string
     */
    public function getNotifications()
    {

        return $this->getData(self::NOTIFICATIONS);
    }

    /**
     * Set code
     *
     * @param string $emails
     *
     * @return FormsInterface
     */
    public function setNotifications($emails)
    {

        return $this->setData(self::NOTIFICATIONS, $emails);
    }

    /**
     * Get store_id
     *
     * @return string
     */
    public function getStoreId()
    {

        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store_id
     *
     * @param string $store_id
     *
     * @return FormsInterface
     */
    public function setStoreId($store_id)
    {

        return $this->setData(self::STORE_ID, $store_id);
    }
}
