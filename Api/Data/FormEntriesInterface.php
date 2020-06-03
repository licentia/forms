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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Forms\Api\Data;

/**
 * Interface FormEntriesInterface
 *
 * @package Licentia\Forms\Api\Data
 */
interface FormEntriesInterface
{

    /**
     *
     */
    const FIELD_12 = 'field_12';

    /**
     *
     */
    const FIELD_3 = 'field_3';

    /**
     *
     */
    const FIELD_7 = 'field_7';

    /**
     *
     */
    const VALIDATED = 'validated';

    /**
     *
     */
    const FIELD_5 = 'field_5';

    /**
     *
     */
    const FIELD_10 = 'field_10';

    /**
     *
     */
    const FIELD_1 = 'field_1';

    /**
     *
     */
    const FIELD_9 = 'field_9';

    /**
     *
     */
    const VALIDATION_CODE = 'validation_code';

    /**
     *
     */
    const FIELD_8 = 'field_8';

    /**
     *
     */
    const ENTRY_ID = 'entry_id';

    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     *
     */
    const FIELD_2 = 'field_2';

    /**
     *
     */
    const FIELD_6 = 'field_6';

    /**
     *
     */
    const FORM_ID = 'form_id';

    /**
     *
     */
    const FIELD_11 = 'field_11';

    /**
     *
     */
    const VALIDATION_EXPIRES_AT = 'validation_expires_at';

    /**
     *
     */
    const SUBSCRIBER_ID = 'subscriber_id';

    /**
     *
     */
    const CREATED_AT = 'created_at';

    /**
     *
     */
    const FIELD_4 = 'field_4';

    /**
     *
     */
    const FIELD_13 = 'field_13';

    /**
     *
     */
    const FIELD_17 = 'field_17';

    /**
     *
     */
    const FIELD_23 = 'field_23';

    /**
     *
     */
    const FIELD_20 = 'field_20';

    /**
     *
     */
    const FIELD_25 = 'field_25';

    /**
     *
     */
    const FIELD_21 = 'field_21';

    /**
     *
     */
    const FIELD_18 = 'field_18';

    /**
     *
     */
    const FIELD_16 = 'field_16';

    /**
     *
     */
    const FIELD_22 = 'field_22';

    /**
     *
     */
    const FIELD_19 = 'field_19';

    /**
     *
     */
    const FIELD_15 = 'field_15';

    /**
     *
     */
    const FIELD_24 = 'field_24';

    /**
     *
     */
    const FIELD_14 = 'field_14';

    /**
     *
     */
    const LINK = 'link';

    /**
     *
     */
    const STORE_ID = 'store_id';

    /**
     *
     */
    const EMAIL = 'email';

    /**
     *
     */
    const REQUIRED_EMAIL_VALIDATION = 'required_email_validation';

    /**
     * Get entry_id
     *
     * @return string|null
     */

    public function getEntryId();

    /**
     * Set entry_id
     *
     * @param string $entry_id
     *
     * @return FormEntriesInterface
     */

    public function setEntryId($entry_id);

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
     * @return FormEntriesInterface
     */

    public function setFormId($form_id);

    /**
     * Get subscriber_id
     *
     * @return string|null
     */

    public function getSubscriberId();

    /**
     * Set subscriber_id
     *
     * @param string $subscriber_id
     *
     * @return FormEntriesInterface
     */

    public function setSubscriberId($subscriber_id);

    /**
     * Get customer_id
     *
     * @return string|null
     */

    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return FormEntriesInterface
     */

    public function setCustomerId($customer_id);

    /**
     * Get created_at
     *
     * @return string|null
     */

    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $created_at
     *
     * @return FormEntriesInterface
     */

    public function setCreatedAt($created_at);

    /**
     * Get validated
     *
     * @return string|null
     */

    public function getValidated();

    /**
     * Set validated
     *
     * @param string $validated
     *
     * @return FormEntriesInterface
     */

    public function setValidated($validated);

    /**
     * Get validation_code
     *
     * @return string|null
     */

    public function getValidationCode();

    /**
     * Set validation_code
     *
     * @param string $validation_code
     *
     * @return FormEntriesInterface
     */

    public function setValidationCode($validation_code);

    /**
     * Get validation_expires_at
     *
     * @return string|null
     */

    public function getValidationExpiresAt();

    /**
     * Set validation_expires_at
     *
     * @param string $validation_expires_at
     *
     * @return FormEntriesInterface
     */

    public function setValidationExpiresAt($validation_expires_at);

    /**
     * Get field_1
     *
     * @return string|null
     */

    public function getField1();

    /**
     * Set field_1
     *
     * @param string $field1
     *
     * @return FormEntriesInterface
     */

    public function setField1($field1);

    /**
     * Get field_2
     *
     * @return string|null
     */

    public function getField2();

    /**
     * Set field_2
     *
     * @param string $field2
     *
     * @return FormEntriesInterface
     */

    public function setField2($field2);

    /**
     * Get field_3
     *
     * @return string|null
     */

    public function getField3();

    /**
     * Set field_3
     *
     * @param string $field3
     *
     * @return FormEntriesInterface
     */

    public function setField3($field3);

    /**
     * Get field_4
     *
     * @return string|null
     */

    public function getField4();

    /**
     * Set field_4
     *
     * @param string $field4
     *
     * @return FormEntriesInterface
     */

    public function setField4($field4);

    /**
     * Get field_5
     *
     * @return string|null
     */

    public function getField5();

    /**
     * Set field_5
     *
     * @param string $field5
     *
     * @return FormEntriesInterface
     */

    public function setField5($field5);

    /**
     * Get field_6
     *
     * @return string|null
     */

    public function getField6();

    /**
     * Set field_6
     *
     * @param string $field6
     *
     * @return FormEntriesInterface
     */

    public function setField6($field6);

    /**
     * Get field_7
     *
     * @return string|null
     */

    public function getField7();

    /**
     * Set field_7
     *
     * @param string $field7
     *
     * @return FormEntriesInterface
     */

    public function setField7($field7);

    /**
     * Get field_8
     *
     * @return string|null
     */

    public function getField8();

    /**
     * Set field_8
     *
     * @param string $field8
     *
     * @return FormEntriesInterface
     */

    public function setField8($field8);

    /**
     * Get field_9
     *
     * @return string|null
     */

    public function getField9();

    /**
     * Set field_9
     *
     * @param string $field9
     *
     * @return FormEntriesInterface
     */

    public function setField9($field9);

    /**
     * Get field_10
     *
     * @return string|null
     */

    public function getField10();

    /**
     * Set field_10
     *
     * @param string $field10
     *
     * @return FormEntriesInterface
     */

    public function setField10($field10);

    /**
     * Get field_11
     *
     * @return string|null
     */

    public function getField11();

    /**
     * Set field_11
     *
     * @param string $field11
     *
     * @return FormEntriesInterface
     */

    public function setField11($field11);

    /**
     * Get field_12
     *
     * @return string|null
     */

    public function getField12();

    /**
     * Set field_12
     *
     * @param string $field12
     *
     * @return FormEntriesInterface
     */

    public function setField12($field12);

    /**
     * Get field_13
     *
     * @return string|null
     */

    public function getField13();

    /**
     * Set field_13
     *
     * @param string $field13
     *
     * @return FormEntriesInterface
     */

    public function setField13($field13);

    /**
     * Get field_14
     *
     * @return string|null
     */

    public function getField14();

    /**
     * Set field_14
     *
     * @param string $field14
     *
     * @return FormEntriesInterface
     */

    public function setField14($field14);

    /**
     * Get field_15
     *
     * @return string|null
     */

    public function getField15();

    /**
     * Set field_15
     *
     * @param string $field15
     *
     * @return FormEntriesInterface
     */

    public function setField15($field15);

    /**
     * Get field_16
     *
     * @return string|null
     */

    public function getField16();

    /**
     * Set field_16
     *
     * @param string $field16
     *
     * @return FormEntriesInterface
     */

    public function setField16($field16);

    /**
     * Get field_17
     *
     * @return string|null
     */

    public function getField17();

    /**
     * Set field_17
     *
     * @param string $field17
     *
     * @return FormEntriesInterface
     */

    public function setField17($field17);

    /**
     * Get field_18
     *
     * @return string|null
     */

    public function getField18();

    /**
     * Set field_18
     *
     * @param string $field18
     *
     * @return FormEntriesInterface
     */

    public function setField18($field18);

    /**
     * Get field_19
     *
     * @return string|null
     */

    public function getField19();

    /**
     * Set field_19
     *
     * @param string $field19
     *
     * @return FormEntriesInterface
     */

    public function setField19($field19);

    /**
     * Get field_20
     *
     * @return string|null
     */

    public function getField20();

    /**
     * Set field_20
     *
     * @param string $field20
     *
     * @return FormEntriesInterface
     */

    public function setField20($field20);

    /**
     * Get field_21
     *
     * @return string|null
     */

    public function getField21();

    /**
     * Set field_21
     *
     * @param string $field21
     *
     * @return FormEntriesInterface
     */

    public function setField21($field21);

    /**
     * Get field_22
     *
     * @return string|null
     */

    public function getField22();

    /**
     * Set field_22
     *
     * @param string $field22
     *
     * @return FormEntriesInterface
     */

    public function setField22($field22);

    /**
     * Get field_23
     *
     * @return string|null
     */

    public function getField23();

    /**
     * Set field_23
     *
     * @param string $field23
     *
     * @return FormEntriesInterface
     */

    public function setField23($field23);

    /**
     * Get field_24
     *
     * @return string|null
     */

    public function getField24();

    /**
     * Set field_24
     *
     * @param string $field24
     *
     * @return FormEntriesInterface
     */

    public function setField24($field24);

    /**
     * Get field_25
     *
     * @return string|null
     */

    public function getField25();

    /**
     * Set field_25
     *
     * @param string $field25
     *
     * @return FormEntriesInterface
     */

    public function setField25($field25);
}
