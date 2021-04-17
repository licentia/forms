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

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class Save extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @param Action\Context                                       $context
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory     $fileFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param \Licentia\Forms\Model\FormsFactory                   $formsFactory
     * @param \Licentia\Forms\Model\FormElementsFactory            $formElementsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory             $formEntriesFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfigInterface
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $fileFactory,
            $registry,
            $pandaHelper,
            $formsFactory,
            $formElementsFactory,
            $formEntriesFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->timezone = $timezone;
        $this->dateFilter = $dateFilter;
        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            /** @var \Licentia\Forms\Model\Forms $model */
            $model = $this->registry->registry('panda_form');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Form no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                if (!isset($data['store_id'])) {
                    $data['store_id'] = [0];
                }
                if (array_search(0, $data['store_id'], true) !== false) {
                    $data['store_id'] = [];
                }
                $data['store_id'] = implode(',', $data['store_id']);

                if (!$data['store_id']) {
                    unset($data['store_id']);
                }

                $model->setData('controller_panda', true);
                $model->addData($data);
                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the Form.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException | \RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Form. '));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'active_tab' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
