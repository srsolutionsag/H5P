<?php return array(
    'root' => array(
        'name' => 'srag/h5p',
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'reference' => 'ced93153f1aee2093a12d8e776ffdefe0bbcd2c6',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'h5p/h5p-core' => array(
            'pretty_version' => '1.27.0',
            'version' => '1.27.0.0',
            'reference' => '829524eaf81fe3f3a295d0e843812be4735f51fc',
            'type' => 'library',
            'install_path' => __DIR__ . '/../h5p/h5p-core',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'h5p/h5p-editor' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '8cd9c7fb9a3668fa553b0e093078e87a1fbcdc6d',
            'type' => 'library',
            'install_path' => __DIR__ . '/../h5p/h5p-editor',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'srag/h5p' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => 'ced93153f1aee2093a12d8e776ffdefe0bbcd2c6',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
