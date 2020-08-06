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

namespace Licentia\Forms\Model;

use Licentia\Forms\Api\Data\FormElementsInterfaceFactory;
use Licentia\Forms\Api\Data\FormElementsSearchResultsInterfaceFactory;
use Licentia\Forms\Api\FormElementsRepositoryInterface;
use Licentia\Forms\Model\ResourceModel\FormElements as ResourceFormElements;
use Licentia\Forms\Model\ResourceModel\FormElements\CollectionFactory as FormElementsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FormElementsRepository
 *
 * @package Licentia\Forms\Model
 */
class FormElementsRepository implements FormElementsRepositoryInterface
{

    /**
     * @var
     */
    protected $FormElementsCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var FormElementsSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var FormElementsFactory
     */
    protected $formElementsFactory;

    /**
     * @var FormElementsCollectionFactory
     */
    protected $formElementsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceFormElements
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var FormElementsInterfaceFactory
     */
    protected $dataFormElementsFactory;

    /**
     * @var
     */
    protected $FormElementsFactory;

    /**
     * @param ResourceFormElements                      $resource
     * @param FormElementsFactory                       $formElementsFactory
     * @param FormElementsInterfaceFactory              $dataFormElementsFactory
     * @param FormElementsCollectionFactory             $formElementsCollectionFactory
     * @param FormElementsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                          $dataObjectHelper
     * @param DataObjectProcessor                       $dataObjectProcessor
     * @param StoreManagerInterface                     $storeManager
     */
    public function __construct(
        ResourceFormElements $resource,
        FormElementsFactory $formElementsFactory,
        FormElementsInterfaceFactory $dataFormElementsFactory,
        FormElementsCollectionFactory $formElementsCollectionFactory,
        FormElementsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->formElementsFactory = $formElementsFactory;
        $this->formElementsCollectionFactory = $formElementsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFormElementsFactory = $dataFormElementsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    ) {

        /* if (empty($formElements->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $formElements->setStoreId($storeId);
        } */
        try {
            $this->resource->save($formElements);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the formElements: %1',
                    $exception->getMessage()
                )
            );
        }

        return $formElements;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($formElementsId)
    {

        $formElement = $this->formElementsFactory->create();
        $formElement->load($formElementsId);
        if (!$formElement->getId()) {
            throw new NoSuchEntityException(__('FormElements with id "%1" does not exist.', $formElementsId));
        }

        return $formElement;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->formElementsCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];

        foreach ($collection as $formElementsModel) {
            $formElementsData = $this->dataFormElementsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $formElementsData,
                $formElementsModel->getData(),
                \Licentia\Forms\Api\Data\FormElementsInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $formElementsData,
                \Licentia\Forms\Api\Data\FormElementsInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    ) {

        try {
            $this->resource->delete($formElements);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the FormElements: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($formElementsId)
    {

        return $this->delete($this->getById($formElementsId));
    }
}
