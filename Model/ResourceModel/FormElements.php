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

namespace Licentia\Forms\Model\ResourceModel;

/**
 * Class Forms
 *
 * @package Licentia\Forms\Model\ResourceModel
 */
class FormElements extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var string
     */
    protected string $_idFieldName = 'element_id';

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct()
    {

        $this->_init('panda_forms_elements', 'element_id');
    }
}
