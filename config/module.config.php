<?php

namespace Necropolis;

return [
    'api_adapters' => [
        'invokables' => [
            'necropolis_resources' => Api\Adapter\NecropolisResourceAdapter::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Necropolis\Controller\Admin\Index' => Controller\Admin\IndexController::class,
            'Necropolis\Controller\Admin\Item' => Controller\Admin\ItemController::class,
            'Necropolis\Controller\Admin\ItemSet' => Controller\Admin\ItemSetController::class,
            'Necropolis\Controller\Admin\Media' => Controller\Admin\MediaController::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Necropolis',
                'class' => 'necropolis',
                'route' => 'admin/necropolis',
                'resource' => 'Necropolis\Controller\Admin\Index',
                'privilege' => 'index',
                'pages' => [
                    [
                        'label' => 'Items', // @translate
                        'route' => 'admin/necropolis/default',
                        'controller' => 'item',
                    ],
                    [
                        'label' => 'Item sets', // @translate
                        'route' => 'admin/necropolis/default',
                        'controller' => 'item-set',
                    ],
                    [
                        'label' => 'Media', // @translate
                        'route' => 'admin/necropolis/default',
                        'controller' => 'media',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'necropolis' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/necropolis',
                            'defaults' => [
                                '__NAMESPACE__' => 'Necropolis\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/:controller[/:action]',
                                    'defaults' => [
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
