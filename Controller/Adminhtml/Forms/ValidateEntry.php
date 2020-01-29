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
 * Class ValidateEntry
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class ValidateEntry extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();

        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Forms\Model\FormEntries $model */
        $model = $this->formEntriesFactory->create()->load($id);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                if ($this->getRequest()->getParam('validate') == 1) {
                    $model->validateEntry(true);
                    $this->messageManager->addSuccessMessage(__('Entry validated'));
                }

                if ($this->getRequest()->getParam('validate') == 0) {
                    $model->setData('validated', 0)->save();
                    $this->messageManager->addSuccessMessage(__('Entry voided. Validation needed'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while validating the Entry.')
                );
            }

            return $resultRedirect->setPath('*/*/entries', ['id' => $model->getFormId()]);
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an Entry to validate.'));

        return $resultRedirect->setPath('*/*/');
    }
}
