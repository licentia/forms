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
 * Class SaveEntry
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class SaveEntry extends \Licentia\Forms\Controller\Adminhtml\Forms
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
     * @param Action\Context                                     $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory   $fileFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date     $dateFilter
     * @param \Licentia\Forms\Model\FormsFactory                 $formsFactory
     * @param \Licentia\Forms\Model\FormElementsFactory          $formElementsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory           $formEntriesFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
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
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
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

        $id = $this->getRequest()->getParam('id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getParams()) {
            $data['form_id'] = $id;

            $data = array_merge_recursive((array) $this->getRequest()->getFiles(), $data);

            try {
                $entry = $this->formEntriesFactory->create();

                if (isset($data['entry_id'])) {
                    $entry->load($data['entry_id']);
                }

                if (!isset($data['store_ids'])) {
                    $data['store_ids'] = [0];
                }

                if (array_search(0, $data['store_ids'], true) !== false) {
                    $data['store_ids'] = [];
                }
                $data['store_ids'] = implode(',', $data['store_ids']);

                $entry->addData($data)
                      ->validateElements()
                      ->save();

                $this->messageManager->addSuccessMessage('Form Entry Saved');

                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/newEntry',
                        [
                            'etid'   => $entry->getId(),
                            'id'     => $this->getRequest()->getParam('id'),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath(
                    '*/*/entries',
                    [
                        'id' => $id,
                    ]
                );
            } catch (\Magento\Framework\Exception\LocalizedException | \RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the form. Check the error log for more information.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/newEntry',
                [
                    'etid'   => $entry->getId(),
                    'id'     => $this->getRequest()->getParam('id'),
                    'active_tab' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath(
            '*/*/entries',
            [
                'id' => $id,
            ]
        );
    }
}
