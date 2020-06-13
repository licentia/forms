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

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class SaveElement extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

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

        $data = $this->getRequest()->getParams();

        if ($data) {
            $eid = $this->getRequest()->getParam('eid');

            /** @var \Licentia\Forms\Model\FormElements $model */
            $model = $this->registry->registry('panda_form_element');

            if (!$model->getId() && $eid) {
                $this->messageManager->addErrorMessage(__('This Element no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $data['form_id'] = $id;

                if (isset($data['code']) && !$data['code']) {
                    $transliterated = \Transliterator::createFromRules(
                        ':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;',
                        \Transliterator::FORWARD
                    );
                    $normalized = $transliterated->transliterate($data['name']);
                    $data['code'] = preg_replace('/\W/', '', strtolower($normalized));
                }

                if (isset($data['options'])) {
                    $data['options'] = preg_replace("/[\r\n]+/", ',', $data['options']);
                }

                $model->addData($data);
                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the Element.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $id,
                            'eid'    => $model->getId(),
                            'tab_id' => 'element_section',
                        ]
                    );
                }

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id'     => $id,
                        'tab_id' => 'element_section',
                    ]
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Form. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $id,
                    'eid'    => $this->getRequest()->getParam('eid'),
                    'tab_id' => 'element_section',
                ]
            );
        }

        return $resultRedirect->setPath(
            '*/*/edit',
            [
                'id'     => $id,
                'tab_id' => 'element_section',
            ]
        );
    }
}
