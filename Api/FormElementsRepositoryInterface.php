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
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Forms\Api;

/**
 * Interface FormElementsRepositoryInterface
 *
 * @package Licentia\Forms\Api
 */
interface FormElementsRepositoryInterface
{

    /**
     * Save FormElements
     *
     * @param \Licentia\Forms\Api\Data\FormElementsInterface $formElements
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    );

    /**
     * Retrieve FormElements
     *
     * @param string $formelementsId
     *
     * @return \Licentia\Forms\Api\Data\FormElementsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($formelementsId);

    /**
     * Retrieve FormElements matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Forms\Api\Data\FormElementsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete FormElements
     *
     * @param \Licentia\Forms\Api\Data\FormElementsInterface $formElements
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Licentia\Forms\Api\Data\FormElementsInterface $formElements
    );

    /**
     * Delete FormElements by ID
     *
     * @param string $formelementsId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($formelementsId);
}
