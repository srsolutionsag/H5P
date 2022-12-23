<#1>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if (!$ilDB->tableExists('rep_robj_xhfp_cnt')) {
    $ilDB->createTable('rep_robj_xhfp_cnt', [
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'library_name' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'library_version' => [
            'type' => 'text',
            'length' => '31',
            'notnull' => '1',
        ],
        'num' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'type' => [
            'type' => 'text',
            'length' => '63',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_cnt');
    $ilDB->addPrimaryKey('rep_robj_xhfp_cnt', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_cont')) {
    $ilDB->createTable('rep_robj_xhfp_cont', [
        'author_comments' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'authors' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'changes' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'content_type' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'content_user_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'default_language' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'disable' => [
            'type' => 'integer',
            'length' => '2',
            'notnull' => '1',
        ],
        'embed_type' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'filtered' => [
            'type' => 'clob',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'license' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'license_extras' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'license_version' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'obj_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'parameters' => [
            'type' => 'clob',
            'notnull' => '1',
        ],
        'parent_type' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'slug' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'sort' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'source' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'title' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'uploaded_files' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'year_from' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'year_to' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_cont');
    $ilDB->addPrimaryKey('rep_robj_xhfp_cont', [
        'content_id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_cont_dat')) {
    $ilDB->createTable('rep_robj_xhfp_cont_dat', [
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'data' => [
            'type' => 'clob',
            'notnull' => '1',
        ],
        'data_id' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'invalidate' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'preload' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'sub_content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_cont_dat');
    $ilDB->addPrimaryKey('rep_robj_xhfp_cont_dat', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_cont_lib')) {
    $ilDB->createTable('rep_robj_xhfp_cont_lib', [
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'dependency_type' => [
            'type' => 'text',
            'length' => '31',
            'notnull' => '1',
        ],
        'drop_css' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'weight' => [
            'type' => 'integer',
            'length' => '2',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_cont_lib');
    $ilDB->addPrimaryKey('rep_robj_xhfp_cont_lib', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_ev')) {
    $ilDB->createTable('rep_robj_xhfp_ev', [
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'content_title' => [
            'type' => 'text',
            'length' => '255',
            'notnull' => '1',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'event_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'library_name' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'library_version' => [
            'type' => 'text',
            'length' => '31',
            'notnull' => '1',
        ],
        'sub_type' => [
            'type' => 'text',
            'length' => '63',
            'notnull' => '1',
        ],
        'type' => [
            'type' => 'text',
            'length' => '63',
            'notnull' => '1',
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_ev');
    $ilDB->addPrimaryKey('rep_robj_xhfp_ev', [
        'event_id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_lib')) {
    $ilDB->createTable('rep_robj_xhfp_lib', [
        'add_to' => [
            'type' => 'text',
            'notnull' => '0',
            'default' => 'null',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'drop_library_css' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'embed_types' => [
            'type' => 'text',
            'length' => '255',
            'notnull' => '1',
        ],
        'fullscreen' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'has_icon' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'major_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'metadata_settings' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'minor_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'name' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'patch_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'preloaded_css' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'preloaded_js' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'restricted' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'runnable' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'semantics' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'title' => [
            'type' => 'text',
            'length' => '255',
            'notnull' => '1',
        ],
        'tutorial_url' => [
            'type' => 'text',
            'length' => '1023',
            'notnull' => '1',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_lib');
    $ilDB->addPrimaryKey('rep_robj_xhfp_lib', [
        'library_id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_lib_ca')) {
    $ilDB->createTable('rep_robj_xhfp_lib_ca', [
        'hash' => [
            'type' => 'text',
            'length' => '64',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_lib_ca');
    $ilDB->addPrimaryKey('rep_robj_xhfp_lib_ca', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_lib_dep')) {
    $ilDB->createTable('rep_robj_xhfp_lib_dep', [
        'dependency_type' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'required_library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_lib_dep');
    $ilDB->addPrimaryKey('rep_robj_xhfp_lib_dep', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_lib_hub')) {
    $ilDB->createTable('rep_robj_xhfp_lib_hub', [
        'categories' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'description' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'example' => [
            'type' => 'text',
            'length' => '511',
            'notnull' => '1',
        ],
        'h5p_major_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'h5p_minor_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'icon' => [
            'type' => 'text',
            'length' => '511',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'is_recommended' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'keywords' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'license' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'mnachine_name' => [
            'type' => 'text',
            'length' => '127',
            'notnull' => '1',
        ],
        'major_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'minor_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'owner' => [
            'type' => 'text',
            'length' => '511',
            'notnull' => '1',
        ],
        'patch_version' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'popularity' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'screenshots' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'summary' => [
            'type' => 'text',
            'notnull' => '1',
        ],
        'title' => [
            'type' => 'text',
            'length' => '255',
            'notnull' => '1',
        ],
        'tutorial' => [
            'type' => 'text',
            'length' => '511',
            'notnull' => '1',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_lib_hub');
    $ilDB->addPrimaryKey('rep_robj_xhfp_lib_hub', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_lib_lng')) {
    $ilDB->createTable('rep_robj_xhfp_lib_lng', [
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'language_code' => [
            'type' => 'text',
            'length' => '31',
            'notnull' => '1',
        ],
        'library_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'translation' => [
            'type' => 'text',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_lib_lng');
    $ilDB->addPrimaryKey('rep_robj_xhfp_lib_lng', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_opt_n')) {
    $ilDB->createTable('rep_robj_xhfp_opt_n', [
        'name' => [
            'type' => 'text',
            'length' => '100',
            'notnull' => '1',
        ],
        'value' => [
            'type' => 'text',
            'notnull' => '0',
            'default' => 'null',
        ],
    ]);

    $ilDB->addPrimaryKey('rep_robj_xhfp_opt_n', [
        'name',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_res')) {
    $ilDB->createTable('rep_robj_xhfp_res', [
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'finished' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'max_score' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'opened' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'score' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'time' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_res');
    $ilDB->addPrimaryKey('rep_robj_xhfp_res', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_solv')) {
    $ilDB->createTable('rep_robj_xhfp_solv', [
        'content_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'finished' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'obj_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'user_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_solv');
    $ilDB->addPrimaryKey('rep_robj_xhfp_solv', [
        'id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_tmp')) {
    $ilDB->createTable('rep_robj_xhfp_tmp', [
        'created_at' => [
            'type' => 'timestamp',
            'notnull' => '1',
        ],
        'path' => [
            'type' => 'text',
            'length' => '255',
            'notnull' => '1',
        ],
        'tmp_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
    ]);

    $ilDB->createSequence('rep_robj_xhfp_tmp');
    $ilDB->addPrimaryKey('rep_robj_xhfp_tmp', [
        'tmp_id',
    ]);
}

if (!$ilDB->tableExists('rep_robj_xhfp_obj')) {
    $ilDB->createTable('rep_robj_xhfp_obj', [
        'is_online' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
        'obj_id' => [
            'type' => 'integer',
            'length' => '8',
            'notnull' => '1',
        ],
        'solve_only_once' => [
            'type' => 'integer',
            'length' => '1',
            'notnull' => '1',
        ],
    ]);

    $ilDB->addPrimaryKey('rep_robj_xhfp_obj', [
        'obj_id',
    ]);
}
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
