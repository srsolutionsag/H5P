<?php return array(
    'root' => array(
        'name' => 'srag/h5p',
        'pretty_version' => 'dev-release_9',
        'version' => 'dev-release_9',
        'reference' => 'e450ff64bea3de0e512596f3ab80a494124c6794',
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
            'reference' => '0a88693b6bfd452f8854fc880496aac3fa983160',
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
            'reference' => 'e450ff64bea3de0e512596f3ab80a494124c6794',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
