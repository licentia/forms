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
 * Class Index
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class Entries extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        /** @var \Licentia\Forms\Model\Forms $model */
        $model = $this->registry->registry('panda_form');

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Forms::forms');
        $resultPage->getConfig()
                   ->getTitle()
                   ->prepend(__('Form Entries in form: %1', $model->getName()));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Form Entries'), __('Form Entries'));

        return $resultPage;
    }
}
