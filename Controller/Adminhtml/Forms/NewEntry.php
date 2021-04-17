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

use Licentia\Forms\Block\Adminhtml\Forms\EditEntry;
use Licentia\Forms\Block\Adminhtml\Forms\EditEntry\Tabs;

/**
 * Class NewEntry
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class NewEntry extends \Licentia\Forms\Controller\Adminhtml\Forms
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
                   ->addBreadcrumb(__('Manage Entries'), __('Manage Entries'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Forms\Model\Forms $model */
        $model = $this->registry->registry('panda_form');

        if (count($model->getActiveElements()) === 0) {
            return $this->_redirect($this->_redirect->getRedirectUrl());
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ? __('Edit Entry') : __('New Entry'), $id ? __('Edit Entry') : __('New Entry'));
        $resultPage->getConfig()->getTitle()->prepend(__('Entries'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Entry'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock(EditEntry::class)
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock(Tabs::class)
                   );

        return $resultPage;
    }
}
