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

namespace Licentia\Forms\Controller\Adminhtml\Forms;

/**
 * Class DeleteEntry
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class DeleteEntry extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();

        $id = $this->getRequest()->getParam('deid');

        $model = $this->formEntriesFactory->create()->load($id);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Entry.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the Entry.')
                );
            }

            return $resultRedirect->setPath('*/*/entries', ['id' => $model->getFormId()]);
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an Entry to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
