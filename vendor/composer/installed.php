<?php return array(
    'root' => array(
        'name' => 'srag/h5p',
        'pretty_version' => 'dev-release_9',
        'version' => 'dev-release_9',
        'reference' => '46bae89e5869d706ece58e5e54b2242af8fd5ca2',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'h5p/h5p-core' => array(
            'pretty_version' => '1.26',
            'version' => '1.26.0.0',
            'reference' => 'f3579c0d28205bf34490ee151c07d43a2ffc3507',
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
            'pretty_version' => 'dev-release_9',
            'version' => 'dev-release_9',
            'reference' => '46bae89e5869d706ece58e5e54b2242af8fd5ca2',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
