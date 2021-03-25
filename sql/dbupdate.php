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
