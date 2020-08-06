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

namespace Licentia\Forms\Api\Data;

/**
 * Interface FormsSearchResultsInterface
 *
 * @package Licentia\Forms\Api\Data
 */
interface FormsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Forms list.
     *
     * @return FormsInterface[]
     */

    public function getItems();

    /**
     * Set form_id list.
     *
     * @param FormsInterface[] $items
     *
     * @return $this
     */

    public function setItems(array $items);
}
