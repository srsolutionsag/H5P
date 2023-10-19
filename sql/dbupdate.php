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
            'notnull' => '0',
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
            'type' => 'integer',
            'length' => '1',
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

if (null === ilWACSecurePath::find('h5p')) {
    $path = new ilWACSecurePath();
    $path->setPath('h5p');
    $path->setCheckingClass(ilObjH5PAccess::class);
    $path->setInSecFolder(false);
    $path->setComponentDirectory('./Customizing/global/plugins/Services/Repository/RepositoryObject/H5P');
    $path->store();
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
<#10>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if (!$ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'machine_name')) {
    $ilDB->addTableColumn('rep_robj_xhfp_lib_hub', 'machine_name', [
        'type' => 'text',
        'length' => '127',
        'notnull' => '1',
    ]);
}
?>
<#11>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_opt_n')) {
    $ilDB->query(
        "
        INSERT IGNORE INTO rep_robj_xhfp_opt_n (`name`, `value`) VALUES ('send_usage_statistics', '0');
    "
    );
}
?>
<#12>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_opt_n')) {
    $ilDB->query(
        "
        UPDATE rep_robj_xhfp_opt_n SET `value` = '0' WHERE `name` = 'send_usage_statistics' AND `value` IS NULL;
    "
    );
}
?>
<#13>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_opt_n')) {
    $ilDB->insert('rep_robj_xhfp_opt_n', [
        'name' => ['text', 'allow_h5p_imports'],
        'value' => ['text', '1'],
    ]);
}
?>
<#14>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableColumnExists('rep_robj_xhfp_lib_hub', 'mnachine_name')) {
    $ilDB->dropTableColumn('rep_robj_xhfp_lib_hub', 'mnachine_name');
}
?>
<#15>
<?php
global $DIC;

// this setting will eventually be used in ILIAS\Filesystem\Security\Sanitizing\FilenameSanitizerImpl
// to allow the suffix "h5p" for file names. This needs to be done for import/exports to work.
$whitelist = $DIC->settings()->get('suffix_custom_white_list', '');
$whitelist = explode(',', $whitelist);

if (!in_array('h5p', $whitelist, true)) {
    $whitelist[] = 'h5p';
    $DIC->settings()->set('suffix_custom_white_list', implode(',', $whitelist));
}
?>
<#16>
<?php
/**
 * This database update step has been introduced because of a bug which referenced
 * library dependencies incorrectly when deprecated content was imported and an old
 * version of a library was installed. The framework referenced the latest installed
 * version of a dependency instead of the version which is specified in the library
 * which is being imported. This lead to scripts not being found or wrong semantics
 * being loaded.
 *
 * The update step cannot rely on the database only, but must scan the installed H5P
 * libraries in the filesystem. The information needed to fix the database is
 * contained in the library.json file, which stores the depencencies in according
 * properties. Since this code is executed by eval(), we cannot properly error-handle
 * this step, which makes it somewhat fragile. Therefore I recommend to create a
 * backup of the 'rep_robj_xhfp_lib_dep' database table, in case something goes wrong.
 *
 * Possibly affected libraries can be limited to those which have been installed the
 * day after the first official release-date of the version which introduced this bug:
 * https://github.com/srsolutionsag/H5P/commit/753120f3506d2b5622c182204a2f63dd34c30e3c
 */

$last_unaffected_create_date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-02-13 59:59:59');
$date_for_updated_libraries = new DateTimeImmutable();
$h5p_library_storage = ILIAS_ABSOLUTE_PATH . '/' . ILIAS_WEB_DIR . '/' . CLIENT_ID . '/h5p/libraries';
$h5p_installed_libraries = (@scandir($h5p_library_storage)) ?: [];

foreach ($h5p_installed_libraries as $h5p_library_dir) {
    if ('.' === substr($h5p_library_dir, 0, 1)) {
        continue;
    }

    $h5p_library_json = (@file_get_contents("$h5p_library_storage/$h5p_library_dir/library.json")) ?: null;
    if (null === $h5p_library_json) {
        continue;
    }

    $h5p_library_data = json_decode($h5p_library_json, true);
    if (!isset($h5p_library_data['machineName'], $h5p_library_data['majorVersion'], $h5p_library_data['minorVersion'])) {
        continue;
    }

    $installed_library = getInstalledLibraryIdAndDate(
        $h5p_library_data['machineName'],
        (int) $h5p_library_data['majorVersion'],
        (int) $h5p_library_data['minorVersion']
    );

    if (null === $installed_library || $installed_library['updated_at'] <= $last_unaffected_create_date) {
        continue;
    }

    $updated = false;
    if (isset($h5p_library_data['preloadedDependencies'])) {
        processDependencies($installed_library['id'], $h5p_library_data['preloadedDependencies'], 'preloaded');
        $updated = true;
    }
    if (isset($h5p_library_data['dynamicDependencies'])) {
        processDependencies($installed_library['id'], $h5p_library_data['dynamicDependencies'], 'dynamic');
        $updated = true;
    }
    if (isset($h5p_library_data['editorDependencies'])) {
        processDependencies($installed_library['id'], $h5p_library_data['editorDependencies'], 'editor');
        $updated = true;
    }

    if ($updated) {
        $ilDB->update('rep_robj_xhfp_lib', [
            'updated_at' => ['timestamp', $date_for_updated_libraries->format('Y-m-d H:i:s')],
        ], [
            'library_id' => ['integer', $installed_library['id']],
        ]);
    }

    $installed_library = null;
    $h5p_library_data = null;
}

function processDependencies(int $library_id, array $dependencies, string $dependency_type): void
{
    /** @var $ilDB ilDBInterface */
    global $ilDB;

    $ilDB->queryF(
        "DELETE FROM rep_robj_xhfp_lib_dep WHERE library_id = %s AND dependency_type = %s;",
        ['integer', 'text'],
        [$library_id, $dependency_type]
    );

    foreach ($dependencies as $dependency) {
        $dependant_library = getInstalledLibraryIdAndDate(
            $dependency['machineName'],
            (int) $dependency['majorVersion'],
            (int) $dependency['minorVersion']
        );

        if (null === $dependant_library) {
            continue;
        }

        $next_id = (int) $ilDB->nextId('rep_robj_xhfp_lib_dep');
        $ilDB->insert(
            'rep_robj_xhfp_lib_dep',
            [
                'id' => ['integer', $next_id],
                'dependency_type' => ['text', $dependency_type],
                'library_id' => ['integer', $library_id],
                'required_library_id' => ['integer', $dependant_library['id']],
            ]
        );
    }
}

/**
 * @return array{id: int, updated_at: DateTimeImmutable|false}|null
 */
function getInstalledLibraryIdAndDate(string $name, int $major_version, int $minor_version): ?array
{
    /** @var $ilDB ilDBInterface */
    global $ilDB;

    $installed_library = $ilDB->fetchAll(
        $ilDB->queryF(
            "SELECT library_id, updated_at FROM rep_robj_xhfp_lib WHERE name = %s AND major_version = %s AND minor_version = %s",
            ['text', 'integer', 'integer'],
            [$name, $major_version, $minor_version]
        )
    );

    if (empty($installed_library[0])) {
        return null;
    }

    return [
        'id' => (int) $installed_library[0]['library_id'],
        'updated_at' => DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $installed_library[0]['updated_at']
        ),
    ];
}
?>
<#17>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableExists('rep_robj_xhfp_cont')) {
    // migrate contents which were affected by a bug where the wrong parent type
    // has been used in the ilH5PPageComponentImporter.
    $ilDB->query("UPDATE rep_robj_xhfp_cont SET parent_type = 'unknown' WHERE parent_type = 'object' AND obj_id = 0;");

    // migrate contents classified as pages to unknown, because we cannot determine
    // the actual parent type the currently stored information.
    $ilDB->query("UPDATE rep_robj_xhfp_cont SET parent_type = 'unknown' WHERE parent_type = 'page';");

    // migrate contents classified as objects to the actual parent type.
    $ilDB->query("UPDATE rep_robj_xhfp_cont SET parent_type = 'xhfp' WHERE parent_type = 'object';");
}

if (!$ilDB->tableColumnExists('rep_robj_xhfp_cont', 'in_workspace')) {
    $ilDB->addTableColumn('rep_robj_xhfp_cont', 'in_workspace', [
        'type' => 'integer',
        'length' => '1',
        'notnull' => '1',
        'default' => '0',
    ]);
}
?>
<#18>
<?php
/**
 * @var $ilDB ilDBInterface
 */
if ($ilDB->tableColumnExists('rep_robj_xhfp_solv', 'content_id')) {
    $ilDB->modifyTableColumn('rep_robj_xhfp_solv', 'content_id', [
        'type' => 'integer',
        'length' => '8',
        'notnull' => '0',
    ]);
}
?>
