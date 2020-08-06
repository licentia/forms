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
 * Interface FormElementsInterface
 *
 * @package Licentia\Forms\Api\Data
 */
interface FormElementsInterface
{

    /**
     *
     */
    const MAX_LENGTH = 'max_length';

    /**
     *
     */
    const MIN_LENGTH = 'min_length';

    /**
     *
     */
    const MAX_NUMBER = 'max_number';

    /**
     *
     */
    const MAX_DATE = 'max_date';

    /**
     *
     */
    const MIN_DATE = 'min_date';

    /**
     *
     */
    const REQUIRED = 'required';

    /**
     *
     */
    const ENTRY_CODE = 'entry_code';

    /**
     *
     */
    const ACTIVE = 'is_active';

    /**
     *
     */
    const MAP = 'map';

    /**
     *
     */
    const MAP_CUSTOMER = 'map_customer';

    /**
     *
     */
    const HTML = 'html';

    /**
     *
     */
    const PARAMS = 'params';

    /**
     *
     */
    const EXTENSIONS = 'extensions';

    /**
     *
     */
    const MAX_SIZE = 'max_size';

    /**
     *
     */
    const MIN_SIZE = 'min_size';

    /**
     *
     */
    const RESIZE = 'resize';

    /**
     *
     */
    const CODE = 'code';

    /**
     *
     */
    const MIN_WIDTH = 'min_width';

    /**
     *
     */
    const MAX_WIDTH = 'max_width';

    /**
     *
     */
    const MIN_HEIGHT = 'min_height';

    /**
     *
     */
    const MAX_HEIGHT = 'max_height';

    /**
     *
     */
    const EMAIL_VALIDATION = 'email_validation';

    /**
     *
     */
    const LINK_EXPIRATION = 'link_expiration';

    /**
     *
     */
    const DEFAULT_VALUE = 'default';

    /**
     *
     */
    const UNIQUE = 'unique';

    /**
     *
     */
    const PLACEHOLDER = 'placeholder';

    /**
     *
     */
    const HINT = 'hint';

    /**
     *
     */
    const ELEMENT_ID = 'element_id';

    /**
     *
     */
    const NAME = 'name';

    /**
     *
     */
    const SORT_ORDER = 'sort_order';

    /**
     *
     */
    const DISABLED = 'disabled';

    /**
     *
     */
    const OPTIONS = 'options';

    /**
     *
     */
    const TYPE = 'type';

    /**
     *
     */
    const ALLOW_MULTIPLE = 'allow_multiple';

    /**
     *
     */
    const SHOW_IN_GRID = 'show_in_grid';

    /**
     *
     */
    const SHOW_IN_FRONTEND = 'show_in_frontend';

    /**
     *
     */
    const STARS = 'stars';

    /**
     *
     */
    const PATTERN = 'pattern';

    /**
     *
     */
    const FORM_ID = 'form_id';

    /**
     *
     */
    const CHECKED = 'checked';

    /**
     *
     */
    const MIN_NUMBER = 'min_number';

    /**
     *
     */
    const CSS_CLASS = 'css_class';

    /**
     * Get element_id
     *
     * @return string|null
     */

    public function getElementId();

    /**
     * Set element_id
     *
     * @param string $element_id
     *
     * @return FormElementsInterface
     */

    public function setElementId($element_id);

    /**
     * Get form_id
     *
     * @return string|null
     */

    public function getFormId();

    /**
     * Set form_id
     *
     * @param string $form_id
     *
     * @return FormElementsInterface
     */

    public function setFormId($form_id);

    /**
     * Get name
     *
     * @return string|null
     */

    public function getName();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FormElementsInterface
     */

    public function setName($name);

    /**
     * Get type
     *
     * @return string|null
     */

    public function getType();

    /**
     * Set type
     *
     * @param string $type
     *
     * @return FormElementsInterface
     */

    public function setType($type);

    /**
     * Get stars
     *
     * @return string|null
     */

    public function getStars();

    /**
     * Set stars
     *
     * @param string $stars
     *
     * @return FormElementsInterface
     */

    public function setStars($stars);

    /**
     * Get required
     *
     * @return string|null
     */

    public function getRequired();

    /**
     * Set required
     *
     * @param string $required
     *
     * @return FormElementsInterface
     */

    public function setRequired($required);

    /**
     * Get unique
     *
     * @return string|null
     */

    public function getUnique();

    /**
     * Set unique
     *
     * @param string $unique
     *
     * @return FormElementsInterface
     */

    public function setUnique($unique);

    /**
     * Get default
     *
     * @return string|null
     */

    public function getDefault();

    /**
     * Set default
     *
     * @param string $default
     *
     * @return FormElementsInterface
     */

    public function setDefault($default);

    /**
     * Get map
     *
     * @return string|null
     */

    public function getMap();

    /**
     * Set map
     *
     * @param string $map
     *
     * @return FormElementsInterface
     */

    public function setMap($map);

    /**
     * Get map_customer
     *
     * @return string
     */
    public function getMapCustomer();

    /**
     * Set map_customer
     *
     * @param $mapCustomer
     *
     * @return  FormElementsInterface
     */
    public function setMapCustomer($mapCustomer);

    /**
     * Get placeholder
     *
     * @return string|null
     */

    public function getPlaceholder();

    /**
     * Set placeholder
     *
     * @param string $placeholder
     *
     * @return FormElementsInterface
     */

    public function setPlaceholder($placeholder);

    /**
     * Get css_class
     *
     * @return string|null
     */

    public function getCssClass();

    /**
     * Set css_class
     *
     * @param string $css_class
     *
     * @return FormElementsInterface
     */

    public function setCssClass($css_class);

    /**
     * Get sort_order
     *
     * @return string|null
     */

    public function getSortOrder();

    /**
     * Set sort_order
     *
     * @param string $sort_order
     *
     * @return FormElementsInterface
     */

    public function setSortOrder($sort_order);

    /**
     * Get checked
     *
     * @return string|null
     */

    public function getChecked();

    /**
     * Set checked
     *
     * @param string $checked
     *
     * @return FormElementsInterface
     */

    public function setChecked($checked);

    /**
     * Get disabled
     *
     * @return string|null
     */

    public function getDisabled();

    /**
     * Set disabled
     *
     * @param string $disabled
     *
     * @return FormElementsInterface
     */

    public function setDisabled($disabled);

    /**
     * Get hint
     *
     * @return string|null
     */

    public function getHint();

    /**
     * Set hint
     *
     * @param string $hint
     *
     * @return FormElementsInterface
     */

    public function setHint($hint);

    /**
     * Get entry_code
     *
     * @return string|null
     */

    public function getEntryCode();

    /**
     * Set entry_code
     *
     * @param string $entry_code
     *
     * @return FormElementsInterface
     */

    public function setEntryCode($entry_code);

    /**
     * Get active
     *
     * @return string|null
     */

    public function getActive();

    /**
     * Set active
     *
     * @param string $active
     *
     * @return FormElementsInterface
     */

    public function setActive($active);

    /**
     * Get options
     *
     * @return string|null
     */

    public function getOptions();

    /**
     * Set options
     *
     * @param string $options
     *
     * @return FormElementsInterface
     */

    public function setOptions($options);

    /**
     * Get pattern
     *
     * @return string|null
     */

    public function getPattern();

    /**
     * Set pattern
     *
     * @param string $pattern
     *
     * @return FormElementsInterface
     */

    public function setPattern($pattern);

    /**
     * Get min_number
     *
     * @return string|null
     */

    public function getMinNumber();

    /**
     * Set min_number
     *
     * @param string $min_number
     *
     * @return FormElementsInterface
     */

    public function setMinNumber($min_number);

    /**
     * Get max_number
     *
     * @return string|null
     */

    public function getMaxNumber();

    /**
     * Set max_number
     *
     * @param string $max_number
     *
     * @return FormElementsInterface
     */

    public function setMaxNumber($max_number);

    /**
     * Get max_length
     *
     * @return string|null
     */

    public function getMaxLength();

    /**
     * Set max_length
     *
     * @param string $max_length
     *
     * @return FormElementsInterface
     */

    public function setMaxLength($max_length);

    /**
     * Get min_length
     *
     * @return string|null
     */

    public function getMinLength();

    /**
     * Set min_length
     *
     * @param string $min_length
     *
     * @return FormElementsInterface
     */

    public function setMinLength($min_length);

    /**
     * Get min_date
     *
     * @return string|null
     */

    public function getMinDate();

    /**
     * Set min_date
     *
     * @param string $min_date
     *
     * @return FormElementsInterface
     */

    public function setMinDate($min_date);

    /**
     * Get max_date
     *
     * @return string|null
     */

    public function getMaxDate();

    /**
     * Set max_date
     *
     * @param string $max_date
     *
     * @return FormElementsInterface
     */

    public function setMaxDate($max_date);

    /**
     * Get html
     *
     * @return string|null
     */

    public function getHtml();

    /**
     * Set html
     *
     * @param string $html
     *
     * @return FormElementsInterface
     */

    public function setHtml($html);

    /**
     * Get params
     *
     * @return string|null
     */

    public function getParams();

    /**
     * Set params
     *
     * @param string $params
     *
     * @return FormElementsInterface
     */

    public function setParams($params);

    /**
     * Get extensions
     *
     * @return string|null
     */

    public function getExtensions();

    /**
     * Set extensions
     *
     * @param string $extensions
     *
     * @return FormElementsInterface
     */

    public function setExtensions($extensions);

    /**
     * Get max_size
     *
     * @return string|null
     */

    public function getMaxSize();

    /**
     * Set max_size
     *
     * @param string $max_size
     *
     * @return FormElementsInterface
     */

    public function setMaxSize($max_size);

    /**
     * Get email_validation
     *
     * @return string|null
     */

    public function getEmailValidation();

    /**
     * Set email_validation
     *
     * @param string $email_validation
     *
     * @return FormElementsInterface
     */

    public function setEmailValidation($email_validation);

    /**
     * Get link_expiration
     *
     * @return string|null
     */

    public function getLinkExpiration();

    /**
     * Set link_expiration
     *
     * @param string $link_expiration
     *
     * @return FormElementsInterface
     */

    public function setLinkExpiration($link_expiration);

    /**
     * Get min_width
     *
     * @return string
     */
    public function getMinWidth();

    /**
     * Set min_width
     *
     * @param string $minWidth
     *
     * @return FormElementsInterface
     */
    public function setMinWidth($minWidth);

    /**
     * Get max_width
     *
     * @return string
     */
    public function getMaxWidth();

    /**
     * Set max_width
     *
     * @param string $maxWidth
     *
     * @return FormElementsInterface
     */
    public function setMaxWidth($maxWidth);

    /**
     * Get min_height
     *
     * @return string
     */
    public function getMinHeight();

    /**
     * Set min_height
     *
     * @param string $minHeight
     *
     * @return FormElementsInterface
     */
    public function setMinHeight($minHeight);

    /**
     * Get max_height
     *
     * @return string
     */
    public function getMaxHeight();

    /**
     * Set max_height
     *
     * @param string $maxHeight
     *
     * @return FormElementsInterface
     */
    public function setMaxHeight($maxHeight);

    /**
     * Get resize
     *
     * @return string
     */
    public function getResize();

    /**
     * Set resize
     *
     * @param string $resize
     *
     * @return FormElementsInterface
     */
    public function setResize($resize);

    /**
     * Get show_in_grid
     *
     * @return string
     */
    public function getShowInGrid();

    /**
     * Set showInGrid
     *
     * @param string $showInGrid
     *
     * @return  FormElementsInterface
     */
    public function setShowInGrid($showInGrid);

    /**
     * Get show_in_frontend
     *
     * @return string
     */
    public function getShowInFrontend();

    /**
     * Set showInFrontend
     *
     * @param string $showInFrontend
     *
     * @return  FormElementsInterface
     */
    public function setShowInFrontend($showInFrontend);

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set resize
     *
     * @param string $code
     *
     * @return FormElementsInterface
     */
    public function setCode($code);
}
