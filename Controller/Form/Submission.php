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
class Submission extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Licentia\Forms\Model\FormEntriesFactory   $formEntriesFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory
    ) {

        parent::__construct($context);

        $this->formEntriesFactory = $formEntriesFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     *
     */
    public function execute()
    {

        $code = $this->getRequest()->getParam('code');

        if (!$code) {
            $this->messageManager->addErrorMessage(__('Missing Code'));
            $this->_redirect('/');
        }

        $entries = $this->formEntriesFactory->create()
                                            ->getCollection()->addFieldToFilter('validation_code', $code);

        if ($entries->getSize() != '1') {
            $this->messageManager->addErrorMessage(__('Entry not found'));
            $this->_redirect('/');
        }

        /** @var \Licentia\Forms\Model\FormEntries $entry */
        $entry = $entries->getFirstItem();

        if (!$entry->getValidated() == '0') {
            $this->messageManager->addErrorMessage(__('Already Validated'));

            return $this->_redirect('/');
        }

        try {
            $form = $entry->getForm();

            $success = $form->getData('success_page');
            $message = $form->getData('success_message');

            if (!$message) {
                $message = 'Successfully Validated. Thank you!';
            }
            $entry->validateEntry();

            $this->messageManager->addSuccessMessage(__($message));

            $resultRedirect = $this->resultRedirectFactory->create();

            if (stripos($success, 'http:') === false) {
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            if (stripos($success, '/') === false) {
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            if (stripos($success, '/') === false) {
                return $resultRedirect->setPath($success);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_redirect('/');
        }
    }
}
