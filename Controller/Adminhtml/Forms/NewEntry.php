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

namespace Licentia\Forms\Controller\Adminhtml\Forms;

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

        if (count($model->getActiveElements()) == 0) {
            return $this->_redirect($this->_redirect->getRedirectUrl());
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ? __('Edit Entry') : __('New Entry'), $id ? __('Edit Entry') : __('New Entry'));
        $resultPage->getConfig()->getTitle()->prepend(__('Entries'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Entry'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\EditEntry')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Forms\Block\Adminhtml\Forms\EditEntry\Tabs')
                   );

        return $resultPage;
    }
}
