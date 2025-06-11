<?php return array(
    'root' => array(
        'name' => 'srag/h5p',
        'pretty_version' => 'dev-release_9',
        'version' => 'dev-release_9',
        'reference' => '896dbee6e32483337249dc96edc8510d5ee33c44',
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
            'reference' => 'f3a60ec2bdbe410cb2b11e8f8cb4cf206c41364b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../h5p/h5p-editor',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'srag/h5p' => array(
            'pretty_version' => 'dev-release_9',
            'version' => 'dev-release_9',
            'reference' => '896dbee6e32483337249dc96edc8510d5ee33c44',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
