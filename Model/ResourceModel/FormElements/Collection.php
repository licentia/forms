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

namespace Licentia\Forms\Model\ResourceModel\FormElements;

/**
 * Class Collection
 *
 * @package Licentia\Forms\Model\ResourceModel\Forms
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_init(\Licentia\Forms\Model\FormElements::class,
            \Licentia\Forms\Model\ResourceModel\FormElements::class);
    }
}
