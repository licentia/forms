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
            } catch (\Magento\Framework\Exception\LocalizedException | \RuntimeException $e) {
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
