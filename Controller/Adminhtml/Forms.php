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

namespace Licentia\Forms\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Forms controller
 */
class Forms extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Licentia_Forms::forms';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected \Magento\Framework\View\Result\LayoutFactory $layoutFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected \Licentia\Forms\Model\FormsFactory $formsFactory;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Forms\Model\FormElementsFactory
     */
    protected \Licentia\Forms\Model\FormElementsFactory $formElementsFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected \Magento\Framework\App\Response\Http\FileFactory $fileFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory;

    /**
     * Forms constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Licentia\Forms\Model\FormsFactory                $formsFactory
     * @param \Licentia\Forms\Model\FormElementsFactory         $formElementsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory          $formEntriesFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->formsFactory = $formsFactory;
        $this->formElementsFactory = $formElementsFactory;
        $this->formEntriesFactory = $formEntriesFactory;
        $this->pandaHelper = $pandaHelper;
        $this->fileFactory = $fileFactory;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->formsFactory->create();
        $entry = $this->formEntriesFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if ($id && !$model->getId() && $this->getRequest()->getActionName() !== 'ValidateEntry') {
            $this->messageManager->addErrorMessage(__('This Form no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $etid = $this->getRequest()->getParam('etid');
        if ($etid) {
            $entry->load($etid);
        }

        if ($etid && !$entry->getId()) {
            $this->messageManager->addErrorMessage(__('This Entry no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        if (!$model->getEntryType()) {
            $type = $this->getRequest()->getParam('ctype', 'frontend');
            $model->setEntryType($type);
        }

        $this->_request->setParams(['ctype' => $model->getEntryType()]);

        $eid = $this->getRequest()->getParam('eid');
        $element = $this->formElementsFactory->create();

        if ($eid) {
            $element->load($eid);
        }

        if ($eid && !$element->getId()) {
            $this->messageManager->addErrorMessage(__('This Element no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->registry->register('panda_form_element', $element, true);

        if ($data = $this->_getSession()->getFormData(true)) {
            if ($this->getRequest()->getActionName() === 'newEntry') {
                $entry->addData($data);
            } else {
                $model->addData($data);
            }
        }

        $this->registry->register('panda_form', $model, true);
        $this->registry->register('panda_form_entry', $entry, true);
    }

}
