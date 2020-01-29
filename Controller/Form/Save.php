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
    protected $resultPageFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

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

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
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
