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

namespace Licentia\Forms\Controller\Form;

/**
 * Class Subscriber
 *
 * @package Licentia\Forms\Controller
 */
class Save extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected \Licentia\Forms\Model\FormsFactory $formsFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor;

    /**
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManagerInterface
     * @param \Magento\Framework\App\Action\Context                 $context
     * @param \Magento\Framework\View\Result\PageFactory            $resultPageFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory              $formEntriesFactory
     * @param \Magento\Customer\Model\Session                       $customerSession
     * @param \Licentia\Forms\Model\FormsFactory                    $formsFactory
     *
     */
    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Forms\Model\FormsFactory $formsFactory
    ) {

        parent::__construct($context);

        $this->formEntriesFactory = $formEntriesFactory;
        $this->formsFactory = $formsFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManagerInterface;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     *
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        $formId = $this->getRequest()->getParam('form_id');
        $data = $this->getRequest()->getParams();
        $data['form_id'] = $formId;
        $data['customer_id'] = $this->customerSession->getId();
        $data['store_id'] = $this->storeManager->getStore()->getId();

        $form = $this->formsFactory->create()->load($formId);

        $data = array_merge((array) $this->getRequest()->getFiles(), $data);

        $success = $form->getSuccessPage();

        if ($form->getSuccessMessage()) {
            $message = __($form->getSuccessMessage());
        } else {
            $message = __('Thanks for your submission');
        }

        try {
            if ($form->getRegisteredOnly() >= 1 && empty($data['customer_id'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please try again'));
            }

            /** @var \Licentia\forms\Model\FormEntries $entry */
            $entry = $this->formEntriesFactory->create()
                                              ->setData($data)
                                              ->validateElements()
                                              ->save();

            if ($entry->getRequiredEmailValidation()) {
                $this->messageManager->addSuccessMessage(
                    __(
                        'You are required to validate your entry. ' .
                        'Please check your email %1 and click in the approval link.',
                        $entry->getEmail()
                    )
                );
            } else {
                $this->messageManager->addSuccessMessage($message);
            }

            $this->dataPersistor->clear('form_data_' . $formId);

            if (stripos($success, 'http:') !== false || stripos($success, 'https:') !== false) {
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            return $resultRedirect->setPath($success);

        } catch (\Magento\Framework\Exception\LocalizedException | \RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the form. Please try again %1', $e->getMessage())
            );
        }

        $this->dataPersistor->set('form_data_' . $formId, $data);

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
