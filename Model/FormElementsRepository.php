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

/**
 * Class FormElementsRepository
 *
 * @package Licentia\Forms\Model
 */
class FormElementsRepository implements FormElementsRepositoryInterface
{

    /**
     * @var FormElementsCollectionFactory
     */
    protected FormElementsCollectionFactory $FormElementsCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var FormElementsSearchResultsInterfaceFactory
     */
    protected FormElementsSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var FormElementsFactory
     */
    protected FormElementsFactory $formElementsFactory;

    /**
     * @var FormElementsCollectionFactory
     */
    protected FormElementsCollectionFactory $formElementsCollectionFactory;

    /**
     * @var ResourceFormElements
     */
    protected ResourceFormElements $resource;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var FormElementsInterfaceFactory
     */
    protected FormElementsInterfaceFactory $dataFormElementsFactory;

    /**
     * @var FormElementsFactory
     */
    protected FormElementsFactory $FormElementsFactory;

    /**
     * @param ResourceFormElements                      $resource
     * @param FormElementsFactory                       $formElementsFactory
     * @param FormElementsInterfaceFactory              $dataFormElementsFactory
     * @param FormElementsCollectionFactory             $formElementsCollectionFactory
     * @param FormElementsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                          $dataObjectHelper
     * @param DataObjectProcessor                       $dataObjectProcessor
     */
    public function __construct(
        ResourceFormElements $resource,
        FormElementsFactory $formElementsFactory,
        FormElementsInterfaceFactory $dataFormElementsFactory,
        FormElementsCollectionFactory $formElementsCollectionFactory,
        FormElementsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {

        $this->resource = $resource;
        $this->formElementsFactory = $formElementsFactory;
        $this->formElementsCollectionFactory = $formElementsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFormElementsFactory = $dataFormElementsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    ) {

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
    public function getById($formelementsId)
    {

        $formElement = $this->formElementsFactory->create();
        $formElement->load($formelementsId);
        if (!$formElement->getId()) {
            throw new NoSuchEntityException(__('FormElements with id "%1" does not exist.', $formelementsId));
        }

        return $formElement;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->formElementsCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
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
