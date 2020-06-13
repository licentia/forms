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
 * Class Delete
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class Delete extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->registry->registry('panda_form');
        $element = $this->registry->registry('panda_form_element');

        $isElement = false;
        $type = 'Form';
        $id = null;
        if ($model->getId() || $element->getId()) {
            try {
                if ($element->getId()) {
                    $isElement = true;
                    $type = 'Element';
                    $element->delete();
                    $id = $element->getFormId();
                }
                if ($model->getId()) {
                    $model->delete();
                    $id = $model->getId();
                }

                $this->messageManager->addSuccessMessage(__('You deleted the %1', $type));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the ' . $type)
                );
            }

            if ($isElement) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            } else {
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an %1 to delete.', $type));

        return $resultRedirect->setPath('*/*/');
    }
}
