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

namespace Licentia\Forms\Controller\Adminhtml\Forms;

/**
 * Class Edit
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class Edit extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Forms::forms')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage Forms'), __('Manage Forms'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Forms\Model\Forms $model */
        $model = $this->registry->registry('panda_form');
        /** @var \Licentia\Forms\Model\FormElements $element */
        $element = $this->registry->registry('panda_form_element');

        if ($element && $element->getId() && $element->getFormId() != $model->getId()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Form no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        $model->setStoreId(explode(',', $model->getStoreId()));

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if (!$model->getId()) {
            $this->messageManager->addNoticeMessage('Please save the form to add Elements');
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ? __('Edit Form') : __('New Form'), $id ? __('Edit Form') : __('New Form'));
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Forms'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Form'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\Edit\Tabs')
                   );

        return $resultPage;
    }
}
