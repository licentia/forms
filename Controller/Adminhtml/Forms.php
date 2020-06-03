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
 * @modified   03/06/20, 16:18 GMT
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
    const ADMIN_RESOURCE = 'Licentia_Forms::forms';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Forms\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Forms\Model\FormElementsFactory
     */
    protected $formElementsFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

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

        /** @var \Licentia\Forms\Model\Forms $model */
        $model = $this->formsFactory->create();
        $entry = $this->formEntriesFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if ($id && !$model->getId() && $this->getRequest()->getActionName() != 'ValidateEntry') {
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
        /** @var \Licentia\Forms\Model\FormElements $element */
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
            if ($this->getRequest()->getActionName() == 'newEntry') {
                $entry->addData($data);
            } else {
                $model->addData($data);
            }
        }

        $this->registry->register('panda_form', $model, true);
        $this->registry->register('panda_form_entry', $entry, true);
    }

}
