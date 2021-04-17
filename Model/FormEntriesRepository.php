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

use Licentia\Forms\Api\Data\FormEntriesInterfaceFactory;
use Licentia\Forms\Api\Data\FormEntriesSearchResultsInterfaceFactory;
use Licentia\Forms\Api\FormEntriesRepositoryInterface;
use Licentia\Forms\Model\ResourceModel\FormEntries as ResourceFormEntries;
use Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory as FormEntriesCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FormEntriesRepository
 *
 * @package Licentia\Forms\Model
 */
class FormEntriesRepository implements FormEntriesRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var
     */
    protected $FormEntriesFactory;

    /**
     * @var
     */
    protected $FormEntriesCollectionFactory;

    /**
     * @var FormEntriesSearchResultsInterfaceFactory
     */
    protected FormEntriesSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var FormEntriesInterfaceFactory
     */
    protected FormEntriesInterfaceFactory $dataFormEntriesFactory;

    /**
     * @var FormEntriesFactory
     */
    protected FormEntriesFactory $formEntriesFactory;

    /**
     * @var FormEntriesCollectionFactory
     */
    protected FormEntriesCollectionFactory $formEntriesCollectionFactory;

    /**
     * @var ResourceFormEntries
     */
    protected ResourceFormEntries $resource;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @param ResourceFormEntries                      $resource
     * @param FormEntriesFactory                       $formEntriesFactory
     * @param FormEntriesInterfaceFactory              $dataFormEntriesFactory
     * @param FormEntriesCollectionFactory             $formEntriesCollectionFactory
     * @param FormEntriesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                         $dataObjectHelper
     * @param DataObjectProcessor                      $dataObjectProcessor
     * @param StoreManagerInterface                    $storeManager
     */
    public function __construct(
        ResourceFormEntries $resource,
        FormEntriesFactory $formEntriesFactory,
        FormEntriesInterfaceFactory $dataFormEntriesFactory,
        FormEntriesCollectionFactory $formEntriesCollectionFactory,
        FormEntriesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->formEntriesFactory = $formEntriesFactory;
        $this->formEntriesCollectionFactory = $formEntriesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFormEntriesFactory = $dataFormEntriesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
    ) {

        /* if (empty($formEntries->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $formEntries->setStoreId($storeId);
        } */
        try {
            $this->resource->save($formEntries);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the formEntries: %1',
                    $exception->getMessage()
                )
            );
        }

        return $formEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($formEntriesId)
    {

        $formEntries = $this->formEntriesFactory->create();
        $formEntries->load($formEntriesId);
        if (!$formEntries->getId()) {
            throw new NoSuchEntityException(__('FormEntries with id "%1" does not exist.', $formEntriesId));
        }

        return $formEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdDisplay($formentriesId)
    {

        return $this->getById($formentriesId)->getEntryToDisplay();
    }

    /**
     * {@inheritdoc}
     */
    public function getListByCode(string $code, $storeId = null)
    {

        return $this->formEntriesFactory->create()->getListByCode($code, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->formEntriesCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
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

        foreach ($collection as $formEntriesModel) {
            $formEntriesData = $this->dataFormEntriesFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $formEntriesData,
                $formEntriesModel->getData(),
                \Licentia\Forms\Api\Data\FormEntriesInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $formEntriesData,
                \Licentia\Forms\Api\Data\FormEntriesInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Licentia\Forms\Api\Data\FormEntriesInterface $formEntries
    ) {

        try {
            $this->resource->delete($formEntries);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the FormEntries: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($formEntriesId)
    {

        return $this->delete($this->getById($formEntriesId));
    }
}
