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

namespace Licentia\Forms\Api;

/**
 * Interface FormsRepositoryInterface
 *
 * @package Licentia\Forms\Api
 */
interface FormsRepositoryInterface
{

    /**
     * Save Forms
     *
     * @param \Licentia\Forms\Api\Data\FormsInterface $forms
     *
     * @return \Licentia\Forms\Api\Data\FormsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Licentia\Forms\Api\Data\FormsInterface $forms
    );

    /**
     * Retrieve Forms
     *
     * @param string $formsId
     *
     * @return \Licentia\Forms\Api\Data\FormsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($formsId);

    /**
     * Retrieve Forms matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Forms\Api\Data\FormsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Forms
     *
     * @param \Licentia\Forms\Api\Data\FormsInterface $forms
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Licentia\Forms\Api\Data\FormsInterface $forms
    );

    /**
     * Delete Forms by ID
     *
     * @param string $formsId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($formsId);
}
