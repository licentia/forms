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

namespace Licentia\Forms\Model;

use Licentia\Forms\Api\Data\FormEntriesInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Forms
 *
 * @package Licentia\Forms\Model
 */
class FormEntries extends \Magento\Framework\Model\AbstractModel implements FormEntriesInterface
{

    /**
     *
     */
    public const XML_PATH_PANDA_FORMS_TEMPLATE = 'panda_forms/forms/template';

    /**
     *
     */
    public const XML_PATH_PANDA_FORMS_NOTIFICATION = 'panda_forms/forms/notify';

    /**
     *
     */
    public const XML_PATH_PANDA_FORMS_NOTIFICATION_TEMPLATE = 'panda_forms/forms/notify_template';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected string $_eventPrefix = 'panda_form_entry';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected string $_eventObject = 'form_entry';

    /**
     * @var FormElementsFactory
     */
    protected FormElementsFactory $formElementsFactory;

    /**
     * @var FormsFactory
     */
    protected FormsFactory $formsFactory;

    /**
     * @var ResourceModel\FormElements\CollectionFactory
     */
    protected ResourceModel\FormElements\CollectionFactory $formElementsCollection;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected \Licentia\Panda\Model\SubscribersFactory $subscribersFactory;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected \Magento\Captcha\Helper\Data $helper;

    /**
     * @var \Magento\Captcha\Observer\CaptchaStringResolver
     */
    protected \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected \Magento\Framework\UrlInterface $urlInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected \Magento\Customer\Model\CustomerFactory $customerFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected \Magento\Framework\Image\AdapterFactory $imageFactory;

    /**
     * @var EncryptorInterface
     */
    protected EncryptorInterface $encryptor;

    /**
     * FormEntries constructor.
     *
     * @param EncryptorInterface                                           $encryptor
     * @param \Magento\Framework\Image\AdapterFactory                      $imageFactory
     * @param \Magento\Customer\Model\CustomerFactory                      $customerFactory
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\UrlInterface                              $url
     * @param \Magento\Framework\App\RequestInterface                      $request
     * @param \Magento\Framework\Mail\Template\TransportBuilder            $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\MediaStorage\Model\File\UploaderFactory             $uploaderFactory
     * @param \Magento\Framework\Filesystem                                $filesystem
     * @param \Licentia\Panda\Model\AutorespondersFactory                  $autorespondersFactory
     * @param FormElementsFactory                                          $formElementsFactory
     * @param FormsFactory                                                 $formsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                     $subscribersFactory
     * @param \Licentia\Panda\Model\ExtraFieldsFactory                     $extraFieldsFactory
     * @param \Magento\Captcha\Helper\Data                                 $helper
     * @param \Magento\Captcha\Observer\CaptchaStringResolver              $captchaStringResolver
     * @param ResourceModel\FormElements\CollectionFactory                 $elementsCollection
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        FormElementsFactory $formElementsFactory,
        FormsFactory $formsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        ResourceModel\FormElements\CollectionFactory $elementsCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->encryptor = $encryptor;
        $this->imageFactory = $imageFactory;
        $this->customerFactory = $customerFactory;
        $this->formsFactory = $formsFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->formElementsFactory = $formElementsFactory;
        $this->formElementsCollection = $elementsCollection;
        $this->autorespondersFactory = $autorespondersFactory;

        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;

        $this->helper = $helper;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->request = $request;

        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;

        $this->urlInterface = $url;
        $this->scopeConfig = $scopeConfig;
        $this->pandaHelper = $pandaHelper;

        $this->customerSession = $customerSession;
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\FormEntries::class);
    }

    /**
     * @return Forms
     */
    public function getForm(): Forms
    {

        if (!$this->getFormId()) {
            $this->setFormId($this->request->getParam('form_id'));
        }

        return $this->formsFactory->create()->load($this->getFormId());
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Validator\Exception
     */
    public function validateEntry($isAdmin = false): FormEntries
    {

        if (!$this->getForm()->isEnabled()) {
            throw  new \Magento\Framework\Validator\Exception(__('Form not found'));
        }

        if ($this->getValidated()) {
            throw  new \Magento\Framework\Validator\Exception(__('Nothing to Validate'));
        }

        if (!$isAdmin) {
            $date = $this->pandaHelper->gmtDate();

            if ($date > $this->getValidationExpiresAt()) {
                throw  new \Magento\Framework\Validator\Exception(__('Unable to Validate. Expired Link'));
            }

            $elements = $this->getForm()->getEmailField();
            $email = $this->getData('field_' . $elements->getData('entry_code'));

            $this->autorespondersFactory->create()->newFormEntry($this, $email);

            $this->notifyNewFormEntry($this);
        }

        return $this->setValidated(1)
                    ->save();
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public function validateUrl($url): bool
    {

        if (!preg_match(
            '/^((http|https):\\/\\/)?[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i',
            $url
        )
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function validateEmail($email): bool
    {

        return !(!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != '');
    }

    /**
     * @param $phone
     *
     * @return bool|string
     */
    public function validatePhone($phone)
    {

        $phone = preg_replace('/[^\-0-9]/', '', $phone);
        $phone = explode('-', $phone);

        if ((count($phone) == 1 || (count($phone) == 2 && strlen($phone[0]) > 3)) &&

            $this->pandaHelper->getCountryCode()) {
            $code = $this->pandaHelper->getCountryCode();
            $countryPrefix = \Licentia\Panda\Helper\Data::getPrefixForCountry($code);
            $phone = $countryPrefix . '-' . implode($code, array_slice($phone, 1));

            $phone = explode('-', $phone);

        }

        if (count($phone) < 2) {
            return false;
        }

        if (strlen($phone[0]) > 3) {
            return false;
        }

        return $phone[0] . '-' . implode('', array_slice($phone, 1));
    }

    /**
     * @param FormEntries $entry
     */
    public function notifyNewFormEntry(FormEntries $entry)
    {

        if (!$this->getForm()->isFrontend()) {
            return;
        }

        $storeId = $this->storeManager->getStore()->getId();

        if ($entry->getForm()->getNotifications()) {
            $notify = $entry->getForm()->getNotifications();
        } else {
            $notify = $this->scopeConfig->getValue(self::XML_PATH_PANDA_FORMS_NOTIFICATION, 'store', $storeId);
        }

        $notify = str_getcsv($notify);
        $notify = array_map('trim', $notify);

        $formElements = $this->getForm()->getActiveElements();

        $fieldsEmail = '';
        $entryArray = $entry->toArray();

        foreach ($formElements as $formElement) {
            if (array_key_exists('field_' . $formElement->getData('entry_code'), $entryArray)) {
                $value = $entryArray['field_' . $formElement->getData('entry_code') . '_rendered'] ?? $entryArray['field_' . $formElement->getData('entry_code')];

                $fieldsEmail .= $formElement->getData('name') . ': ' . $value . "<br>";
            }
        }

        $template = 'panda_forms_forms_notify_template';
        foreach ($notify as $email) {
            try {
                $custom = $this->scopeConfig->getValue(
                    self::XML_PATH_PANDA_FORMS_NOTIFICATION_TEMPLATE,
                    'store',
                    $this->storeManager->getStore()
                                       ->getId()
                );

                if ($custom) {
                    $template = $custom;
                }

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($template)
                    ->setTemplateOptions(
                        [
                            'area'  => 'frontend',
                            'store' => $this->storeManager->getStore()
                                                          ->getId(),
                        ]
                    )
                    ->setTemplateVars(['list' => $fieldsEmail, 'entry' => $entry, 'form' => $entry->getForm()])
                    ->setFrom('support')
                    ->addTo($email)
                    ->getTransport();

                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->pandaHelper->logWarning($e);
            }
        }
    }

    /**
     * @return $this
     */
    public function afterLoad(): FormEntries
    {

        parent::afterLoad();

        $this->prepareForDisplay();

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function prepareForDisplay(): \Magento\Framework\Model\AbstractModel
    {

        /** @var FormElements $element */
        foreach ($this->getForm()->getElements()->addFieldToFilter('type', ['in' => ['image', 'file']]) as $element) {
            if ($this->getData('field_' . $element->getEntryCode())) {
                $files = json_decode($this->getData('field_' . $element->getEntryCode()));

                if ($files === false || $files === null) {
                    $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                                 ->getRelativePath(
                                                     $this->getData('field_' . $element->getEntryCode())
                                                 );

                    $url = $this->storeManager->getStore()
                                              ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                    $this->setData('field_' . $element->getEntryCode() . '_rendered', $url . $mediaDir);
                } elseif (is_array($files)) {
                    $urls = [];

                    foreach ($files as $file) {
                        $file = ltrim($file, '/');

                        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                                     ->getRelativePath($file);

                        $url = $this->storeManager->getStore()
                                                  ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                        $urls[] = $url . $mediaDir;
                    }

                    $this->setData('field_' . $element->getEntryCode() . '_rendered', implode(' | ', $urls));
                }
            }
        }

        if (!$this->getForm()->isFrontend()) {
            $parse = $this->getForm()->getElements()->addFieldToFilter('type', 'textarea');

            foreach ($parse as $element) {
                $parsedContent = $this->pandaHelper->getTemplateProcessor()
                                                   ->filter($this->getData('field_' . $element->getEntryCode()));

                $this->setData('field_' . $element->getEntryCode() . '_rendered', $parsedContent);
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterSave(): \Magento\Framework\Model\AbstractModel
    {

        if (!$this->getRequiredEmailValidation()) {
            $elements = $this->getForm()->getEmailField();

            $email = $this->getData('field_' . $elements->getData('entry_code'));

            $this->notifyNewFormEntry($this);

            $this->autorespondersFactory->create()->newFormEntry($this, $email);

            return parent::afterSave();
        }
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_PANDA_FORMS_TEMPLATE,
                        'store',
                        $this->storeManager->getStore()
                                           ->getId()
                    )
                )
                ->setTemplateOptions(
                    [
                        'area'  => 'frontend',
                        'store' => $this->storeManager->getStore()
                                                      ->getId(),
                    ]
                )
                ->setTemplateVars(['data' => $this, 'form' => $this->getForm()])
                ->setFrom('support')
                ->addTo($this->getEmail())
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->pandaHelper->logException($e);
        }

        return parent::afterSave();
    }

    /**
     * @return $this
     */
    public function loadDataFromRequest(): FormEntries
    {

        $formId = $this->request->getParam('form_id');
        $data = $this->request->getPostValue();
        if ($formId) {
            $data['form_id'] = $formId;
        }
        $data['customer_id'] = $this->customerSession->getId();
        $data['store_id'] = $this->storeManager->getStore()->getId();

        $data = array_merge((array) $this->request->getFiles(), $data);

        $this->addData($data);

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Validator\Exception
     */
    public function validateElements(): \Magento\Framework\Model\AbstractModel
    {

        $form = $this->getForm();
        $id = false;

        if (($form->getCanEdit() && $this->getCustomerId()) ||
            ($form->getData('manage_subscription') && $this->getCustomerId())) {

            /** @var self $id */
            $id = $this->getCollection()
                       ->addFieldToFilter('form_id', $form->getId())
                       ->addFieldToFilter('customer_id', $this->getCustomerId())
                       ->setOrder('entry_id', 'DESC')
                       ->setPageSize(1)
                       ->getFirstItem();

            $this->setId($id->getId());
        }

        if ($form->getData('manage_subscription') && $this->getData('subscriber')) {

            /** @var self $id */
            $id = $this->getCollection()
                       ->addFieldToFilter('form_id', $form->getId())
                       ->addFieldToFilter('subscriber_id', $this->getData('subscriber')->getId())
                       ->setOrder('entry_id', 'DESC')
                       ->setPageSize(1)
                       ->getFirstItem();

            $this->setId($id->getId());
        }

        $elements = $form->getActiveElements();
        // image, file, cpatcha
        $specialElements = [];

        $map = [];
        $customerMap = [];
        $errors = [];

        if (\Zend_Validate::is(trim($this->getData('hideit')), 'NotEmpty')) {
            throw  new \Magento\Framework\Validator\Exception(__('Wrong field supplied'));
        }

        $deleteFiles = [];
        /** @var FormElements $element */
        foreach ($elements as $element) {
            $field = Forms::FIELD_IDENTIFIER . $element->getEntryCode();

            $value = $this->getData($field);

            if (in_array($element->getType(), ['file', 'image', 'captcha'])) {
                $specialElements[] = Forms::FIELD_IDENTIFIER . $element->getEntryCode();
            }

            if ($element->getType() === 'captcha') {
                $formId = 'panda_forms';

                /** @var \Magento\Captcha\Model\DefaultModel $captcha */
                $captcha = $this->helper->getCaptcha($formId);

                if ($captcha->isRequired() && !$captcha->isCorrect($this->captchaStringResolver->resolve($this->request,
                        $formId))) {
                            $errors[$field] = __('Invalid Captcha');
                            continue;
                        }
            }

            if (!$value && $element->getRequired() &&
                $element->getType() !== 'file' && $element->getType() !== 'image') {
                $errors[$field] = __('%1 Field is Required', __($element->getName()));
                continue;
            }

            if ($this->getId() &&
                ($element->getType() === 'file' || $element->getType() === 'image') &&
                empty($value['name'])) {
                $this->unsetData('panda_' . $element->getEntryCode());
            }

            if ($this->getId() &&
                ($element->getType() === 'file' || $element->getType() === 'image') &&
                $this->getData('panda_' . $element->getEntryCode() . '_delete')
            ) {
                if ($id && $id->getId()) {
                    $newValue = json_decode($id->getData('field_' . $element->getEntryCode()), true);

                    if (is_array($newValue) && isset($newValue[0])) {
                        $fileExists = $newValue[0];

                        $path = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                                 ->isFile($fileExists);

                        if ($path) {
                            $deleteFiles[] = $fileExists;
                        }
                    }
                    $this->setData('panda_' . $element->getEntryCode(), '');
                }
            }

            $valErrors = [];

            if ($value) {
                $valErrors = $this->validateElement($element, $value);
            }

            if ($valErrors) {
                $errors = array_merge($errors, $valErrors);
            }

            if (!$valErrors && ($element->getType() === 'image' || $element->getType() === 'file') && is_array($value)) {

                foreach ($value as $index => $upload) {
                    if ($index > 0 && ($index + 1) > $element->getAllowMultiple()) {
                        continue;
                    }
                    if (isset($upload['name']) && empty($upload['name'])) {
                        unset($value[$index]);
                    }
                }

            }

            if ($element->getType() === 'email' && $element->getEmailValidation() && !$this->getId()) {
                $date = (int) $form->getData('link_expiration');

                if ($date == 0) {
                    $date = (new \DateTime($this->pandaHelper->gmtDate()))
                        ->add(new \DateInterval('P10Y'))
                        ->format('Y-m-d H:i:s');
                } else {
                    $date = (new \DateTime($this->pandaHelper->gmtDate()))
                        ->add(new \DateInterval('PT' . $date . 'H'))
                        ->format('Y-m-d H:i:s');
                }

                $this->setValidated(0);
                $this->setValidationExpiresAt($date);
                $this->setEmail($value);
                $this->setRequiredEmailValidation(true);
                $this->setValidationCode(\Licentia\Panda\Helper\Data::getToken(30));

                $this->setLink(
                    $this->urlInterface->getUrl(
                        'pandaf/form/submission',
                        ['code' => $this->getValidationCode(),]
                    )
                );
                $this->setData('form_title', $form->getTitle());
            }

            if (count($errors) > 0) {
                continue;
            }

            if ($element->getDisabled()) {
                $this->setData($field, $element->getDefault());
            }

            if ($element->getType() === 'checkbox' && $value !== 'checked') {
                $this->setData($field, 'unchecked');
            }

            if ($form->getRegisteredOnly() && $element->getMapCustomer()) {
                $customerMap[$element->getMapCustomer()] = $value;
            }

            if ($element->getType() === 'cellphone' && $element->getMap()) {
                $map['cellphone'] = $value;
            } elseif ($element->getType() === 'text' && $element->getMap() === 'firstname') {
                $map['firstname'] = $value;
            } elseif ($element->getType() === 'text' && $element->getMap() === 'lastname') {
                $map['lastname'] = $value;
            } elseif ($element->getType() === 'text' && $element->getMap() === 'dob') {
                $map['dob'] = $value;
            } elseif ($element->getType() === 'email' && $element->getMap()) {
                $map['email'] = $value;
            } elseif ($element->getMap()) {
                if (is_array($value)) {
                    $value = \Licentia\Panda\Helper\Data::arrayToCsv($value);
                }

                $map['field_' . $element->getMap()] = $value;

                $default = $this->extraFieldsFactory->create()->load($element->getEntryCode(), 'entry_code');

                if (!$value) {
                    $map['field_' . $element->getMap()] = $default->getData('default');
                }
            }

            if (!$value) {
                $value = $element->getDefault();
                $this->setData($field, $value);
            }
        }

        if ($errors) {
            throw  new \Magento\Framework\Validator\Exception(__(implode(' | ' . PHP_EOL, $errors)));
        }

        try {
            foreach ($deleteFiles as $file) {
                $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                 ->delete($file);
            }
        } catch (\Exception $e) {
        }

        foreach ($this->getData() as $key => $value) {
            if (stripos($key, Forms::FIELD_IDENTIFIER) !== false ||
                (!in_array($key, $specialElements, true) && is_array($value))) {

                if (is_array($value) && (count($value) != count($value, COUNT_RECURSIVE))) {
                    $value = '';
                }

                if (is_array($value)) {
                    $value = \Licentia\Panda\Helper\Data::arrayToCsv($value);
                }

                $this->setData(str_replace(Forms::FIELD_IDENTIFIER, 'field_', $key), $value);
                $this->unsetData($key);
            }
        }
        if (isset($map['email']) || $this->getCustomerId()) {

            $subscriber = $this->subscribersFactory->create();

            if (isset($map['email'])) {
                $subscriber->loadSubscriber($map['email'], $this->storeManager);
            } elseif ($this->getCustomerId()) {
                $subscriber = $subscriber->loadByCustomerId($this->getCustomerId());
            }

            if ($subscriber->getId() || isset($map['email'])) {
                foreach ($map as $key => $value) {
                    $subscriber->setData($key, $value);
                }

                $subscriber->setFormId($this->getFormId());
                $subscriber->setStoreId($this->getStoreId());

                if (!$subscriber->getId()) {
                    $subscriber->setStatus(1);
                }

                try {
                    $subscriber->save();
                } catch (\Exception $e) {
                }

                $this->setSubscriberId($subscriber->getId());
            }
        }

        if ($customerMap && $this->getCustomerId()) {
            $customer = $this->customerFactory->create()->load($this->getCustomerId());
            try {
                if ($customer->getId()) {
                    $customer->addData($customerMap)->save();
                }
            } catch (\Exception $e) {
            }
        }

        return parent::validateBeforeSave();
    }

    /**
     * @param      $fieldName
     * @param      $fieldValue
     * @param null $formId
     *
     * @return bool
     */
    public function validateUnique($fieldName, $fieldValue, $formId = null): bool
    {

        $fieldName = str_replace(Forms::FIELD_IDENTIFIER, 'field_', $fieldName);

        if (null === $formId) {
            $formId = $this->getFormId();
        }

        $form = $this->formsFactory->create()->load($formId);

        $collection = $this->getCollection()
                           ->addFieldToFilter('form_id', $formId)
                           ->addFieldToFilter($fieldName, $fieldValue);

        if ($form->getCanEdit() && $this->getCustomerId()) {
            $collection->addFieldToFilter('customer_id', ['neq' => $this->getCustomerId()]);
        }

        if ($this->getEntryId()) {
            $collection->addFieldToFilter('entry_id', ['neq' => $this->getEntryId()]);
        }

        return $collection->getSize() == 0;
    }

    /**
     * @param FormElements $element
     * @param              $value
     *
     * @return bool
     */
    public function validateElement(FormElements $element, $value): bool
    {

        $errors = false;

        $field = Forms::FIELD_IDENTIFIER . $element->getEntryCode();

        if ($element->getType() === 'phone') {
            $phone = $this->validatePhone($value);

            if (!$phone) {
                $errors[$field] = __(
                    'Invalid number %1 in %2. Please use: CountryCode-PhoneNumber. Ex: 351-913241234',
                    $value,
                    __($element->getName())
                );
            } else {
                $this->setData('phone', $phone);
            }
        }

        if ($element->getType() === 'email' && !$this->validateEmail($value)) {
            $errors[$field] = __('Invalid email in %1', __($element->getName()));
        }

        if ($element->getType() === 'url' && !$this->validateUrl($value)) {
            $errors[$field] = __('Invalid URL %1 in %2', $value, __($element->getName()));
        }

        if ($element->getType() === 'number' && !is_numeric($value)) {
            $errors[$field] = __(
                'Invalid number %1 in %2. Please use: CountryCode-PhoneNumber. Ex: 351-913241234',
                $value,
                __($element->getName())
            );
        }

        if ($element->getType() === 'date') {
            try {
                $date = new \DateTime($value);
                $result = $date->format('Y-m-d');
                $this->setData($field, $result);

                if ($element->getMinDate() && $element->getMinDate() < $result) {
                    $errors[$field] = __(
                        'Please choose a date newer than %1 in %2',
                        $element->getMinDate(),
                        __($element->getName())
                    );
                }

                if ($element->getMaxDate() && $element->getMaxDate() < $result) {
                    $errors[$field] = __(
                        'Please choose a date greater than %1 in %2',
                        $element->getMaxDate(),
                        __($element->getName())
                    );
                }
            } catch (\Exception $e) {
                $errors[$field] = __('Invalid date %1 in %2', $value, __($element->getName()));
            }
        }

        if (($element->getType() === 'image' || $element->getType() === 'file') &&
            is_array($value) &&
            isset($value[0]) &&
            isset($value[0]['name']) && $value[0]['name'] != '' &&
            isset($value[0]['type']) && $value[0]['type'] != '') {
            $dir = 'panda/forms/files/' . $this->getFormId() . '/' . \Licentia\Panda\Helper\Data::getToken(32) . '/';

            try {
                if (!$this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->isExist($dir)
                ) {
                    $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                     ->create($dir);
                }

                $path = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                         ->getAbsolutePath($dir);

                $newDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                           ->getRelativePath($dir);

                $tmpValue = [];
                foreach ($value as $index => $upload) {
                    if ($index > 0 && ($index + 1) > $element->getAllowMultiple()) {
                        continue;
                    }

                    $uploader = $this->uploaderFactory->create(['fileId' => $field . '[' . $index . ']']);

                    if ($element->getExtensions()) {
                        $extensions = explode(',', $element->getExtensions());
                        $extensions = array_map('trim', $extensions);
                        $uploader->setAllowedExtensions($extensions);
                    }

                    $uploader->setAllowCreateFolders(false);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $fileSize = ceil($uploader->getFileSize() / 1024 / 1024);

                    if ($element->getMaxSize() < $fileSize) {
                        $errors[$field] = __(
                            'File size greater than allowed %1/%2 in %3',
                            $fileSize,
                            $element->getMaxSize(),
                            __($element->getName())
                        );
                    }

                    if (!$errors) {
                        $fileName = \Licentia\Panda\Helper\Data::getToken();
                        $uploader->save($path, $fileName . '.' . $uploader->getFileExtension());

                        $file = $newDir . $uploader->getUploadedFileName();
                        $filePath = $path . $uploader->getUploadedFileName();

                        if ($element->getType() === 'image') {
                            if (!$element->getResize()) {
                                $size = getimagesize($filePath);

                                if (($element->getMaxWidth() && $element->getMaxWidth() > $size[0]) ||
                                    ($element->getMaxHeight() && $element->getMaxHeight() > $size[1]) ||
                                    ($element->getMinHeight() && $element->getMinHeight() < $size[1]) ||
                                    ($element->getMinWidth() && $element->getMinWidth() < $size[0])) {
                                    $errors[$field] = __(
                                        'Error in field %1: %2 Invalid Dimensions',
                                        __($element->getName(), $e->getMessage())
                                    );

                                    $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->delete($file);
                                }
                            } else {
                                $imageResize = $this->imageFactory->create();
                                $imageResize->open($filePath);
                                $imageResize->constrainOnly(true);
                                $imageResize->keepTransparency(true);
                                $imageResize->keepFrame(true);
                                $imageResize->keepAspectRatio(true);
                                $imageResize->resize($element->getMaxWidth(), $element->getMaxHeight());
                                $imageResize->save($filePath);
                            }
                        }

                        if ($element->getEncrypted()) {
                            file_put_contents($filePath, $this->encryptor->encrypt(file_get_contents($filePath)));
                        }

                        $protectPath = trim($this->scopeConfig->getValue('panda_forms/forms/protect'));
                        if ($element->getProtected() && $protectPath) {

                            if (!is_writable($protectPath)) {
                                $errors[$field] = __('Error in field %1: %2. Cannot save.', __($element->getName()));
                            }

                            $fileProtect = rtrim($protectPath, '/') . '/' . $file;

                            if (!is_dir(pathinfo($fileProtect)['dirname'])) {
                                if (!mkdir($concurrentDirectory = pathinfo($fileProtect)['dirname'], 0755,
                                        true) && !is_dir($concurrentDirectory)) {
                                    throw new \RuntimeException(sprintf('Directory "%s" was not created',
                                        $concurrentDirectory));
                                }
                            }

                            rename($filePath, $fileProtect);

                        }

                        $tmpValue[] = $file;
                    }
                }

                $this->setData($field, json_encode($tmpValue));
            } catch (\Exception $e) {
                $errors[$field] = __('Error in field %1: %2', __($element->getName()), $e->getMessage());
            }
        } elseif (($element->getType() === 'image' || $element->getType() === 'file') &&
                  ((isset($value['delete']) && $value['delete'] == 1) ||
                   (isset($value[1]['delete']) && $value[1]['delete'] == 1))) {
            $file = null;

            if (isset($value['value'])) {
                $file = $value['value'];
            } elseif (isset($value[2]['value'])) {
                $file = $value[2]['value'];
            }

            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->delete($file);

            $this->setData('field_' . $element->getEntryCode(), '');
            $this->setData('panda_' . $element->getEntryCode(), '');
        }

        if ($element->getUnique() && !$this->validateUnique($field, $value)) {
            $errors[$field] = __('Duplicated value %1 in %2', $value, __($element->getName()));
        }

        if ($element->getMaxLength() && strlen($value) > $element->getMaxLength()) {
            $errors[$field] = __('Maximum field length exceeded in %1', __($element->getName()));
        }

        if ($element->getMinLength() && strlen($value) < $element->getMinLength()) {
            $errors[$field] = __('Minimum field length not met in %1', __($element->getName()));
        }

        if ($element->getOptions()) {
            $options = str_getcsv($element->getOptions());
            $options = array_filter($options);

            if ($element->getMap()) {
                $optionsExtraField = $this->extraFieldsFactory->create()->load($element->getMap(), 'entry_code');

                if ($optionsExtraField->getOptions()) {
                    $options = str_getcsv($optionsExtraField->getData('options'));
                }
            }

            $options = array_map('trim', $options);

            foreach ((array) $value as $item) {
                $item = trim($item);
                if (!in_array($item, $options, true) && $item) {
                    $errors[$field] = __('Unexpected value %1 in %2', $item, __($element->getName()));
                }
            }
        }

        return $errors;
    }

    /**
     * @param string $formCode
     * @param null   $storeId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getListByCode(string $formCode, $storeId = null): array
    {

        $form = $this->formsFactory->create()->load($formCode, 'code');

        if (!$form->getId()) {
            throw new LocalizedException(__('Form not Found'));
        }

        if (!$form->isEnabled()) {
            throw new LocalizedException(__('Form not Enabled'));
        }

        $entries = $this->getCollection()
                        ->addFieldToFilter('form_id', $form->getId())
                        ->addFieldToFilter('validated', 1);

        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $entries->getSelect()->where('FIND_IN_SET(?,store_ids) OR store_ids IS NULL', $storeId);

        $this->setFormId($form->getId());

        $return = [];
        /** @var self $entry */
        foreach ($entries as $entry) {
            $return[$entry->getEntryId()] = $this->getEntryToDisplay($entry);
        }

        return $return;
    }

    /**
     * @param FormEntries $data
     *
     * @return array
     */
    public function getEntryToDisplay($data = null): array
    {

        $form = $this->getForm();
        $elements = $form->getActiveElements();

        if ($elements->count() == 0) {
            return [];
        }

        if ($data) {
            $this->addData($data->getData());
            $this->prepareForDisplay();
        }

        $entry = [];
        /** @var FormElements $element */
        foreach ($elements as $element) {
            $value = $this->getData('field_' . $element->getData('entry_code'));

            if ($this->getData('field_' . $element->getData('entry_code') . '_rendered')) {
                $value = $this->getData('field_' . $element->getData('entry_code') . '_rendered');
            }

            if ($element->getOptions()) {
                $value = str_getcsv($value);
            }

            $entry[$element->getCode()] = [
                'name'  => $element->getName(),
                'value' => $value,
                'id'    => $element->getEntryCode(),
                'type'  => $element->getType(),
                'code'  => $element->getCode(),
            ];
        }

        $entry[] = [
            'name'  => (string) __('Created At'),
            'id'    => 'created_at',
            'code'  => 'created_at',
            'value' => $this->getCreatedAt(),
            'type'  => 'Date',
        ];

        $entry[] = [
            'name'  => (string) __('Validated'),
            'id'    => 'validated',
            'code'  => 'validated',
            'value' => $this->getValidated(),
            'type'  => 'bool',
        ];

        return $entry;
    }

    /**
     * Get entry_id
     *
     * @return string
     */
    public function getEntryId(): string
    {

        return $this->getData(self::ENTRY_ID);
    }

    /**
     * Set entry_id
     *
     * @param string $entry_id
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setEntryId($entry_id): FormEntries
    {

        return $this->setData(self::ENTRY_ID, $entry_id);
    }

    /**
     * Get form_id
     *
     * @return string
     */
    public function getFormId(): string
    {

        return $this->getData(self::FORM_ID);
    }

    /**
     * Set form_id
     *
     * @param string $form_id
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setFormId($form_id): FormEntries
    {

        return $this->setData(self::FORM_ID, $form_id);
    }

    /**
     * Get subscriber_id
     *
     * @return string
     */
    public function getSubscriberId(): string
    {

        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * Set subscriber_id
     *
     * @param string $subscriber_id
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setSubscriberId($subscriber_id): FormEntries
    {

        return $this->setData(self::SUBSCRIBER_ID, $subscriber_id);
    }

    /**
     * Get customer_id
     *
     * @return string
     */
    public function getCustomerId(): string
    {

        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setCustomerId($customer_id): FormEntries
    {

        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * Get created_at
     *
     * @return string
     */
    public function getCreatedAt(): string
    {

        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     *
     * @param string $created_at
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setCreatedAt($created_at): FormEntries
    {

        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Get validated
     *
     * @return string
     */
    public function getValidated(): string
    {

        return $this->getData(self::VALIDATED);
    }

    /**
     * Set validated
     *
     * @param string $validated
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setValidated($validated): FormEntries
    {

        return $this->setData(self::VALIDATED, $validated);
    }

    /**
     * Get validation_code
     *
     * @return string
     */
    public function getValidationCode(): string
    {

        return $this->getData(self::VALIDATION_CODE);
    }

    /**
     * Set validation_code
     *
     * @param string $validation_code
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setValidationCode($validation_code): FormEntries
    {

        return $this->setData(self::VALIDATION_CODE, $validation_code);
    }

    /**
     * Get validation_expires_at
     *
     * @return string
     */
    public function getValidationExpiresAt(): string
    {

        return $this->getData(self::VALIDATION_EXPIRES_AT);
    }

    /**
     * Set validation_expires_at
     *
     * @param string $validation_expires_at
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setValidationExpiresAt($validation_expires_at): FormEntries
    {

        return $this->setData(self::VALIDATION_EXPIRES_AT, $validation_expires_at);
    }

    /**
     * Get field_1
     *
     * @return string
     */
    public function getField1(): string
    {

        return $this->getData(self::FIELD_1);
    }

    /**
     * Set field_1
     *
     * @param string $field1
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField1($field1): FormEntries
    {

        return $this->setData(self::FIELD_1, $field1);
    }

    /**
     * Get field_2
     *
     * @return string
     */
    public function getField2(): string
    {

        return $this->getData(self::FIELD_2);
    }

    /**
     * Set field_2
     *
     * @param string $field2
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField2($field2): FormEntries
    {

        return $this->setData(self::FIELD_2, $field2);
    }

    /**
     * Get field_3
     *
     * @return string
     */
    public function getField3(): string
    {

        return $this->getData(self::FIELD_3);
    }

    /**
     * Set field_3
     *
     * @param string $field3
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField3($field3): FormEntries
    {

        return $this->setData(self::FIELD_3, $field3);
    }

    /**
     * Get field_4
     *
     * @return string
     */
    public function getField4(): string
    {

        return $this->getData(self::FIELD_4);
    }

    /**
     * Set field_4
     *
     * @param string $field4
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField4($field4): FormEntries
    {

        return $this->setData(self::FIELD_4, $field4);
    }

    /**
     * Get field_5
     *
     * @return string
     */
    public function getField5(): string
    {

        return $this->getData(self::FIELD_5);
    }

    /**
     * Set field_5
     *
     * @param string $field5
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField5($field5): FormEntries
    {

        return $this->setData(self::FIELD_5, $field5);
    }

    /**
     * Get field_6
     *
     * @return string
     */
    public function getField6(): string
    {

        return $this->getData(self::FIELD_6);
    }

    /**
     * Set field_6
     *
     * @param string $field6
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField6($field6): FormEntries
    {

        return $this->setData(self::FIELD_6, $field6);
    }

    /**
     * Get field_7
     *
     * @return string
     */
    public function getField7(): string
    {

        return $this->getData(self::FIELD_7);
    }

    /**
     * Set field_7
     *
     * @param string $field7
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField7($field7): FormEntries
    {

        return $this->setData(self::FIELD_7, $field7);
    }

    /**
     * Get field_8
     *
     * @return string
     */
    public function getField8(): string
    {

        return $this->getData(self::FIELD_8);
    }

    /**
     * Set field_8
     *
     * @param string $field8
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField8($field8): FormEntries
    {

        return $this->setData(self::FIELD_8, $field8);
    }

    /**
     * Get field_9
     *
     * @return string
     */
    public function getField9(): string
    {

        return $this->getData(self::FIELD_9);
    }

    /**
     * Set field_9
     *
     * @param string $field9
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField9($field9): FormEntries
    {

        return $this->setData(self::FIELD_9, $field9);
    }

    /**
     * Get field_10
     *
     * @return string
     */
    public function getField10(): string
    {

        return $this->getData(self::FIELD_10);
    }

    /**
     * Set field_10
     *
     * @param string $field10
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField10($field10): FormEntries
    {

        return $this->setData(self::FIELD_10, $field10);
    }

    /**
     * Get field_11
     *
     * @return string
     */
    public function getField11(): string
    {

        return $this->getData(self::FIELD_11);
    }

    /**
     * Set field_11
     *
     * @param string $field11
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField11($field11): FormEntries
    {

        return $this->setData(self::FIELD_11, $field11);
    }

    /**
     * Get field_12
     *
     * @return string
     */
    public function getField12(): string
    {

        return $this->getData(self::FIELD_12);
    }

    /**
     * Set field_12
     *
     * @param string $field12
     *
     * @return FormEntries \Licentia\Forms\Api\Data\FormEntriesInterface
     */
    public function setField12($field12): FormEntries
    {

        return $this->setData(self::FIELD_12, $field12);
    }

    /**
     * Get field_13
     *
     * @return string
     */
    public function getField13(): string
    {

        return $this->getData(self::FIELD_13);
    }

    /**
     * Set field_13
     *
     * @param string $field13
     *
     * @return FormEntriesInterface
     */
    public function setField13($field13): FormEntriesInterface
    {

        return $this->setData(self::FIELD_13, $field13);
    }

    /**
     * Get field_14
     *
     * @return string
     */
    public function getField14(): string
    {

        return $this->getData(self::FIELD_14);
    }

    /**
     * Set field_14
     *
     * @param string $field14
     *
     * @return FormEntriesInterface
     */
    public function setField14($field14): FormEntriesInterface
    {

        return $this->setData(self::FIELD_14, $field14);
    }

    /**
     * Get field_15
     *
     * @return string
     */
    public function getField15(): string
    {

        return $this->getData(self::FIELD_15);
    }

    /**
     * Set field_15
     *
     * @param string $field15
     *
     * @return FormEntriesInterface
     */
    public function setField15($field15): FormEntriesInterface
    {

        return $this->setData(self::FIELD_15, $field15);
    }

    /**
     * Get field_16
     *
     * @return string
     */
    public function getField16(): string
    {

        return $this->getData(self::FIELD_16);
    }

    /**
     * Set field_16
     *
     * @param string $field16
     *
     * @return FormEntriesInterface
     */
    public function setField16($field16): FormEntriesInterface
    {

        return $this->setData(self::FIELD_16, $field16);
    }

    /**
     * Get field_17
     *
     * @return string
     */
    public function getField17(): string
    {

        return $this->getData(self::FIELD_17);
    }

    /**
     * Set field_17
     *
     * @param string $field17
     *
     * @return FormEntriesInterface
     */
    public function setField17($field17): FormEntriesInterface
    {

        return $this->setData(self::FIELD_17, $field17);
    }

    /**
     * Get field_18
     *
     * @return string
     */
    public function getField18(): string
    {

        return $this->getData(self::FIELD_18);
    }

    /**
     * Set field_18
     *
     * @param string $field18
     *
     * @return FormEntriesInterface
     */
    public function setField18($field18): FormEntriesInterface
    {

        return $this->setData(self::FIELD_18, $field18);
    }

    /**
     * Get field_19
     *
     * @return string
     */
    public function getField19(): string
    {

        return $this->getData(self::FIELD_19);
    }

    /**
     * Set field_19
     *
     * @param string $field19
     *
     * @return FormEntriesInterface
     */
    public function setField19($field19): FormEntriesInterface
    {

        return $this->setData(self::FIELD_19, $field19);
    }

    /**
     * Get field_20
     *
     * @return string
     */
    public function getField20(): string
    {

        return $this->getData(self::FIELD_20);
    }

    /**
     * Set field_20
     *
     * @param string $field20
     *
     * @return FormEntriesInterface
     */
    public function setField20($field20): FormEntriesInterface
    {

        return $this->setData(self::FIELD_20, $field20);
    }

    /**
     * Get field_21
     *
     * @return string
     */
    public function getField21(): string
    {

        return $this->getData(self::FIELD_21);
    }

    /**
     * Set field_21
     *
     * @param string $field21
     *
     * @return FormEntriesInterface
     */
    public function setField21($field21): FormEntriesInterface
    {

        return $this->setData(self::FIELD_21, $field21);
    }

    /**
     * Get field_22
     *
     * @return string
     */
    public function getField22(): string
    {

        return $this->getData(self::FIELD_22);
    }

    /**
     * Set field_22
     *
     * @param string $field22
     *
     * @return FormEntriesInterface
     */
    public function setField22($field22): FormEntriesInterface
    {

        return $this->setData(self::FIELD_22, $field22);
    }

    /**
     * Get field_23
     *
     * @return string
     */
    public function getField23(): string
    {

        return $this->getData(self::FIELD_23);
    }

    /**
     * Set field_23
     *
     * @param string $field23
     *
     * @return FormEntriesInterface
     */
    public function setField23($field23): FormEntriesInterface
    {

        return $this->setData(self::FIELD_23, $field23);
    }

    /**
     * Get field_24
     *
     * @return string
     */
    public function getField24(): string
    {

        return $this->getData(self::FIELD_24);
    }

    /**
     * Set field_24
     *
     * @param string $field24
     *
     * @return FormEntriesInterface
     */
    public function setField24($field24): FormEntriesInterface
    {

        return $this->setData(self::FIELD_24, $field24);
    }

    /**
     * Get field_25
     *
     * @return string
     */
    public function getField25(): string
    {

        return $this->getData(self::FIELD_25);
    }

    /**
     * Set field_25
     *
     * @param string $field25
     *
     * @return FormEntriesInterface
     */
    public function setField25($field25): FormEntriesInterface
    {

        return $this->setData(self::FIELD_25, $field25);
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): FormEntries
    {

        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @param bool $requiredEmailValidation
     *
     * @return $this
     */
    public function setRequiredEmailValidation($requiredEmailValidation): FormEntries
    {

        return $this->setData(self::REQUIRED_EMAIL_VALIDATION, $requiredEmailValidation);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {

        return $this->getData(self::EMAIL);
    }

    /**
     * @return bool
     */
    public function getRequiredEmailValidation(): bool
    {

        return $this->getData(self::REQUIRED_EMAIL_VALIDATION);
    }

    /**
     * @param $link
     *
     * @return $this
     */
    public function setLink($link): FormEntries
    {

        return $this->setData(self::LINK, $link);
    }

    /**
     * @return mixed
     */
    public function getLink()
    {

        return $this->getData(self::LINK);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): FormEntries
    {

        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {

        return $this->getData(self::STORE_ID);
    }
}
