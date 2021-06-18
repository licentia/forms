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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class View
 *
 * @package Licentia\Forms\Controller\Adminhtml\Forms
 */
class View extends \Licentia\Forms\Controller\Adminhtml\Forms
{

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * View constructor.
     *
     * @param \Magento\Framework\Controller\Result\RawFactory   $rawFactory
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Framework\Filesystem                     $filesystem
     * @param EncryptorInterface                                $encryptor
     * @param \Magento\Backend\App\Action\Context               $context
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\RawFactory $rawFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        EncryptorInterface $encryptor,
        \Magento\Backend\App\Action\Context $context,
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

        $this->rawFactory = $rawFactory;
        $this->encryptor = $encryptor;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $id = $this->getRequest()->getParam('viewid');
        $field = $this->getRequest()->getParam('field');
        $item = $this->getRequest()->getParam('item');

        /** @var \Licentia\Forms\Model\FormEntries $model */
        $model = $this->formEntriesFactory->create()->load($id);

        /** @var \Licentia\Forms\Model\Forms $form */
        $form = $this->registry->registry('panda_form');

        /** @var \Licentia\Forms\Model\FormElements $element */
        $element = $form->getElements()
                        ->addFieldToFilter('entry_code', str_replace('field_', '', $field))
                        ->getFirstItem();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {

            $nValue = json_decode($model->getData($field), true);

            if (is_array($nValue) && isset($nValue[$item])) {

                $protectPath = trim($this->scopeConfig->getValue('panda_forms/forms/protect'));
                $nValue[$item] = $protectPath . $nValue[$item];

                $finalFile = $nValue[$item];

                if ($element->getProtected() && is_file($nValue[$item])) {

                    $file = file_get_contents($finalFile);

                    $fileName = pathinfo($finalFile)['basename'];

                    if ($element->getEncrypted()) {
                        $file = $this->encryptor->decrypt($file);
                    }

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: attachment; filename="' . $fileName . '";');
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');

                    return $this->rawFactory->create()->setContents($file);

                } else {

                    $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                                 ->getRelativePath($finalFile);

                    $url = $this->storeManager->getStore()
                                              ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                    return $resultRedirect->setUrl($url . $mediaDir);

                }

            }
        }

        $this->messageManager->addErrorMessage(__('File not found'));

        return $resultRedirect->setPath('*/*/entries', ['id' => $model->getFormId()]);
    }
}
