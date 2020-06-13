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

namespace Licentia\Forms\Controller\Form;

/**
 * Class Subscriber
 *
 * @package Licentia\Forms\Controller
 */
class Validate extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Validate constructor.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Licentia\Forms\Model\FormEntriesFactory         $formEntriesFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory
    ) {

        parent::__construct($context);

        $this->formEntriesFactory = $formEntriesFactory;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     *
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        $params = $this->getRequest()->getParams();
        $type = $this->getRequest()->getParam('type');

        $field = false;
        $fieldValue = '';
        foreach ($params as $index => $param) {
            if (stripos($index, 'panda_') !== false) {
                $field = $index;
                $fieldValue = $param;
            }
        }

        if (!$field) {
            return $result->setData(['content' => __('Invalid Request')]);
        }

        $formEntry = [];

        if ($type == 'url') {
            $formEntry = $this->formEntriesFactory->create()->validateUrl($fieldValue);
            if (!$formEntry) {
                $formEntry = __('Invalid URL');
            }
        }

        if ($type == 'phone') {
            $formEntry = $this->formEntriesFactory->create()->validatePhone($fieldValue);
            if (!$formEntry) {
                $formEntry = __(
                    'Invalid phone format. Please use: countryCode-Number. ' .
                    'Eg: 1-555 555 555, 351-979544443, 44-0848 9123 456'
                );
            } else {
                $formEntry = true;
            }
        }

        if ($type == 'unique') {

            /** @var \Licentia\Forms\Model\FormEntries $formEntry */
            $formEntry = $this->formEntriesFactory->create();
            $formEntry->setCustomerId($this->customerSession->getCustomerId());

            $formEntry = $formEntry->validateUnique($field, $fieldValue, $this->getRequest()->getParam('form_id'));

            if (!$formEntry) {
                $formEntry = __('The value in this field has been used before');
            }
        }

        return $result->setData(['content' => $formEntry]);
    }
}
