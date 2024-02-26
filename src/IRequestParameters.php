<?php

namespace srag\Plugins\H5P;

/**
 * Note that this class can be transformed into an Enum once ILIAS
 * versions support PHP >= 8.1.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IRequestParameters
{
    // ILIAS request parameters:
    public const USER_ID = 'usr_id';
    public const OBJ_ID = 'obj_id';
    public const REF_ID = 'ref_id';
    public const WSP_ID = 'wsp_id';
    public const TARGET = 'target';

    // Plugin request parameters:
    public const IN_WORKSPACE = 'in_wsp';
    public const LIBRARY_NAME = 'h5p_library_name';
    public const SUB_CONTENT_ID = 'h5p_sub_content_id';
    public const DATA_TYPE = 'h5p_data_type';
    public const CONTENT_ID = 'h5p_content_id';
    public const RESULT_ID = 'h5p_result_id';
    public const MIGRATION_DATA = 'h5p_migration_data';
}
