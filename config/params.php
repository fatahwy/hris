<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;

$env = require __DIR__ . '/env.php';

$pdfHeader = [
    'L' => [
        'content' => 'asdasdasd',
    ],
    'C' => [
        'content' => 'CENTER CONTENT (HEAD)',
        // 'content' => '',
        'font-size' => 10,
        'font-style' => 'B',
        'font-family' => 'arial',
        'color' => '#333333',
    ],
    'R' => [
        'content' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
    ],
    // 'line' => true,
];

$pdfFooter = [
    'L' => [
        'content' => '',
    ],
    'C' => [
        'content' => '',
    ],
    // 'R' => [
    //     'content' => 'RIGHT CONTENT (FOOTER)',
    //     'font-size' => 10,
    //     'color' => '#333333',
    //     'font-family' => 'arial',
    // ],
    // 'line' => true,
];

return [
    'secret' => 'as_@123jkas_$2',
    'bsVersion' => '5.x',
    'adminEmail' => 'fatahwidiyanto11@gmail.com',
    'senderEmail' => $env['senderEmail'],
    'senderName' => 'Example.com mailer',
    // 'bsDependencyEnabled' => false,
    'kartikConfig' => [
        'fileInput' => [
            'showRemove' => false,
            'showUpload' => false,
            'showCancel' => false,
            'overwriteInitial' => true,
            'initialPreviewAsData' => true,
            'previewFileType' => 'image',
            'maxFileSize' => 5 * 1024,
            'allowedExtensions' => ['jpg', 'png', 'jpeg', 'webp'],
        ]
    ],
    'gridConfig' => [
        'autoXlFormat' => true,
        'export' => [
            'skipExportElements' => ['.d-none'],
            'showConfirmAlert' => false,
            'target' => GridView::TARGET_BLANK
        ],
        'exportConfig' => [
            GridView::PDF => [
                'filename' => "download",
                'config' => [
                    'mode' => 'c',
                    'format' => 'A4',
                    'orientation' => 'P',
                    'cssInline' => '.kv-grid-table {font-size:12px;}'
                        . '.table-sm td, .table-sm th {padding: 0px;}'
                        . '.kv-page-summary{background-color: white;}',
                    'methods' => [
                        'SetHeader' => null,
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter]
                        ],
                    ],
                ]
            ],
            GridView::EXCEL => [
                'filename' => "download",
            ]
        ],
        'showPageSummary' => true,
        'pageSummaryContainer' => ['class' => 'text-end'],
        'pageSummaryRowOptions' => ['class' => 'kv-page-summary'],
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container', 'timeout' => 20000]],
        'condensed' => true,
        'resizeStorageKey' => 'fath-' . date("m"),
        'responsiveWrap' => false,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        // 'perfectScrollbar' => true,
        'headerRowOptions' => [
            'class' => 'text-center header-row',
        ],
        'panelHeadingTemplate' => '
            <div class="float-start">
                {summary}
            </div>
            <div class="float-end">
                {toolbar}
            </div>
        ',
        'panelBeforeTemplate' => '
            <div class="float-end">
                {export}
            </div>
        ',
        // {toggleData}
        'panelTemplate' => '
            {panelHeading}
            {items}
            {panelFooter}
        ',
        // {panelBefore}
        'toolbar' => [
            '{export}',
            // '{toggleData}',
        ],
        // custom manual
        'responsive' => true,
        'hover' => true,
        'striped' => false, // Removing striped for a cleaner minimal look
        'bordered' => false,
        'tableOptions' => [
            'class' => 'table table-hover table-modern mb-0',
        ],
        'panel' => [
            'type' => '',
            'after' => false,
            'footer' => false,
            'options' => ['class' => 'card modern-grid-panel mb-4']
        ],
    ],
    'exportConfig' => [
        'filename' => 'download',
        // 'target' => ExportMenu::TARGET_BLANK,
        'pjaxContainerId' => 'kv-pjax-container',
        'showColumnSelector' => false,
        'showConfirmAlert' => false,
        'clearBuffers' => true,
        'exportConfig' => [
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
            ExportMenu::FORMAT_PDF => false,
        ],
    ]
];