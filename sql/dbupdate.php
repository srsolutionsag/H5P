<#1>
<?php
\srag\Plugins\H5P\Repository::getInstance()->installTables();
?>
<#2>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_lib')) {
    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'add_to')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'add_to', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'drop_library_css')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'drop_library_css', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'metadata_settings')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'metadata_settings', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'preloaded_css')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'preloaded_css', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'preloaded_js')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'preloaded_js', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib', 'semantics')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib', 'semantics', array(
            'type' => 'clob',
        ));
    }
}
?>
<#3>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_lib_hub')) {
    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'categories')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'categories', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'description')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'description', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'keywords')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'keywords', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'license')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'license', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'screenshots')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'screenshots', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'summary')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_hub', 'summary', array(
            'type' => 'clob',
        ));
    }
}
?>
<#4>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_lib_lng')) {
    if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_lng', 'translation')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_lib_lng', 'translation', array(
            'type' => 'clob',
        ));
    }
}
?>
<#5>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_opt_n')) {
    if ($ilDB->tableColumnExists('rep_robj_xhfp_opt_n', 'value')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_opt_n', 'value', array(
            'type' => 'clob',
        ));
    }
}
?>
<#6>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_cont')) {
    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'author_comments')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'author_comments', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'authors')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'authors', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'changes')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'changes', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'default_language')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'default_language', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'license')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'license', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'license_extras')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'license_extras', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'license_version')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'license_version', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'source')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'source', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'title')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'title', array(
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'uploaded_files')) {
        $ilDB->modifyTableColumn('rep_robj_xhfp_cont', 'uploaded_files', array(
            'type' => 'clob',
        ));
    }
}
?>
<#7>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_cont')) {
    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'authors')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'authors', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'author') &&
        $ilDB->tableColumnExists('rep_robj_xhfp_cont', 'authors')
    ) {
        $authors = $ilDB->fetchAll($ilDB->query("SELECT content_id, author FROM rep_robj_xhfp_cont;"));

        // migrate author content to new column decode it to a json array.
        // due to #PLH5P-159, SQLs JSON_ARRAY function cannot be used for
        // this operation because ILIAS supports versions from 5.6.
        foreach ($authors as $entry) {
            $author_json_string = json_encode([$entry['author']]);
            $ilDB->manipulateF(
                "UPDATE rep_robj_xhfp_cont SET authors = %s WHERE content_id = %s;",
                ['text', 'integer'],
                [
                    $author_json_string,
                    (int) $entry['content_id'],
                ]
            );
        }

        // drop the old author column.
        $ilDB->dropTableColumn('rep_robj_xhfp_cont', 'author');
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'description')) {
        $ilDB->dropTableColumn('rep_robj_xhfp_cont', 'description');
    }

    if ($ilDB->tableColumnExists('rep_robj_xhfp_cont', 'keywords')) {
        $ilDB->dropTableColumn('rep_robj_xhfp_cont', 'keywords');
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'author_comments')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'author_comments', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'changes')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'changes', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'default_language')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'default_language', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'license_extras')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'license_extras', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'license_version')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'license_version', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'source')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'source', array(
            'notnull' => '1',
            'type' => 'clob',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'year_from')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'year_from', array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '8',
        ));
    }

    if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'year_to')) {
        $ilDB->addTableColumn('rep_robj_xhfp_cont', 'year_to', array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '8',
        ));
    }
}
?>
<#8>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_lib') &&
    !$ilDB->tableColumnExists('rep_robj_xhfp_lib', 'metadata_settings')
) {
    $ilDB->addTableColumn('rep_robj_xhfp_lib', 'metadata_settings', array(
        'notnull' => '1',
        'type' => 'clob',
    ));
}
?>
<#9>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_cont') &&
    $ilDB->tableColumnExists('rep_robj_xhfp_cont', 'authors')
) {
    // fixes #PLH5P-155 where empty authors lead to an empty string
    // entry in the encoded json array instead of an entirely empty
    // json array. due to #PLH5P-159, SQLs JSON_ARRAY function cannot
    // be used for this operation as well because ILIAS supports
    // versions from 5.6.
    $escaped_where_clause = '\'[""]\'';
    $ilDB->manipulate("UPDATE rep_robj_xhfp_cont SET authors = '[]' WHERE authors = $escaped_where_clause");
}
?>