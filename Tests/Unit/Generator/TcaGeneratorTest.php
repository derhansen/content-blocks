<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Generator;

use TYPO3\CMS\ContentBlocks\Backend\Preview\PreviewRenderer;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\TestLoader;
use TYPO3\CMS\Core\EventDispatcher\NoopEventDispatcher;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TcaGeneratorTest extends UnitTestCase
{
    public static function checkTcaFieldTypesDataProvider(): iterable
    {
        yield 'Two simple content block create two types and two columns in tt_content table' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'path' => 'EXT:foo/ContentBlocks/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'bodytext',
                                'type' => 'Textarea',
                                'useExistingField' => true,
                                'properties' => [
                                    'enableRichtext' => true,
                                ],
                            ],
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                                'properties' => [
                                    'default' => 'Default value',
                                    'placeholder' => 'Placeholder text',
                                ],
                            ],
                            [
                                'identifier' => 'palette_1',
                                'type' => 'Palette',
                                'fields' => [
                                    [
                                        'identifier' => 'textarea',
                                        'type' => 'Textarea',
                                    ],
                                    [
                                        'type' => 'Linebreak',
                                    ],
                                    [
                                        'identifier' => 'number',
                                        'type' => 'Number',
                                    ],
                                ],
                            ],
                            [
                                'identifier' => 'email',
                                'type' => 'Email',
                            ],
                            [
                                'identifier' => 'check',
                                'type' => 'Checkbox',
                            ],
                            [
                                'identifier' => 'color',
                                'type' => 'Color',
                            ],
                            [
                                'identifier' => 'file',
                                'type' => 'File',
                                'enableImageManipulation' => 0,
                            ],
                            [
                                'identifier' => 'category',
                                'type' => 'Category',
                            ],
                            [
                                'identifier' => 'datetime',
                                'type' => 'DateTime',
                            ],
                            [
                                'identifier' => 'tab_1',
                                'type' => 'Tab',
                            ],
                            [
                                'identifier' => 'select',
                                'type' => 'Select',
                            ],
                            [
                                'identifier' => 'link',
                                'type' => 'Link',
                            ],
                            [
                                'identifier' => 'radio',
                                'type' => 'Radio',
                            ],
                            [
                                'identifier' => 'reference',
                                'type' => 'Reference',
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'useAsLabel' => 'text2',
                                'fields' => [
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'tab_1',
                                        'type' => 'Tab',
                                    ],
                                    [
                                        'identifier' => 'text2',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'palette_inline',
                                        'type' => 'Palette',
                                        'fields' => [
                                            [
                                                'identifier' => 'palette_field1',
                                                'type' => 'Text',
                                            ],
                                            [
                                                'type' => 'Linebreak',
                                            ],
                                            [
                                                'identifier' => 'palette_field2',
                                                'type' => 'Text',
                                            ],
                                        ],
                                    ],
                                    [
                                        'identifier' => 'collection2',
                                        'type' => 'Collection',
                                        'fields' => [
                                            [
                                                'identifier' => 'text',
                                                'type' => 'Text',
                                            ],
                                            [
                                                'identifier' => 'text2',
                                                'type' => 'Text',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 't3ce/testblock',
                    'path' => 'EXT:foo/ContentBlocks/testblock',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'bodytext',
                                'type' => 'Textarea',
                                'useExistingField' => true,
                            ],
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                                'properties' => [
                                    'default' => '',
                                    'placeholder' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'tt_content' => [
                    'ctrl' => [
                        'typeicon_classes' => [
                            't3ce_example' => 't3ce_example-icon',
                            't3ce_testblock' => 't3ce_testblock-icon',
                        ],
                        'searchFields' => 'header,header_link,subheader,bodytext,pi_flexform,t3ce_example_text,t3ce_example_textarea,t3ce_example_email,t3ce_testblock_text',
                    ],
                    'types' => [
                        't3ce_example' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,bodytext,t3ce_example_text,--palette--;;t3ce_example_palette_1,t3ce_example_email,t3ce_example_check,t3ce_example_color,t3ce_example_file,t3ce_example_category,t3ce_example_datetime,--div--;LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:tabs.tab_1,t3ce_example_select,t3ce_example_link,t3ce_example_radio,t3ce_example_reference,t3ce_example_collection,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'bodytext' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:bodytext.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:bodytext.description',
                                    'config' => [
                                        'enableRichtext' => true,
                                    ],
                                ],
                                't3ce_example_text' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.description',
                                    'config' => [
                                        'default' => 'Default value',
                                        'placeholder' => 'Placeholder text',
                                    ],
                                ],
                                't3ce_example_textarea' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:textarea.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:textarea.description',
                                    'config' => [],
                                ],
                                't3ce_example_number' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:number.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:number.description',
                                    'config' => [],
                                ],
                                't3ce_example_email' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:email.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:email.description',
                                    'config' => [],
                                ],
                                't3ce_example_check' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:check.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:check.description',
                                    'config' => [],
                                ],
                                't3ce_example_color' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:color.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:color.description',
                                    'config' => [],
                                ],
                                't3ce_example_file' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:file.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:file.description',
                                    'config' => [],
                                ],
                                't3ce_example_category' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:category.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:category.description',
                                    'config' => [],
                                ],
                                't3ce_example_datetime' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:datetime.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:datetime.description',
                                    'config' => [],
                                ],
                                't3ce_example_select' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:select.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:select.description',
                                    'config' => [],
                                ],
                                't3ce_example_link' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:link.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:link.description',
                                    'config' => [],
                                ],
                                't3ce_example_radio' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:radio.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:radio.description',
                                    'config' => [],
                                ],
                                't3ce_example_reference' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:reference.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:reference.description',
                                    'config' => [],
                                ],
                                't3ce_example_collection' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                        't3ce_testblock' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,bodytext,t3ce_testblock_text,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                't3ce_testblock_text' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:text.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:text.description',
                                    'config' => [],
                                ],
                                'bodytext' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:bodytext.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:bodytext.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                    ],
                    'columns' => [
                        't3ce_example_text' => [
                            'config' => [
                                'type' => 'input',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_textarea' => [
                            'config' => [
                                'type' => 'text',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_number' => [
                            'config' => [
                                'type' => 'number',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_email' => [
                            'config' => [
                                'type' => 'email',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_check' => [
                            'config' => [
                                'type' => 'check',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_color' => [
                            'config' => [
                                'type' => 'color',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_file' => [
                            'config' => [
                                'type' => 'file',
                                'foreign_table' => 'sys_file_reference',
                                'foreign_field' => 'uid_foreign',
                                'foreign_sortby' => 'sorting_foreign',
                                'foreign_table_field' => 'tablenames',
                                'foreign_match_fields' => [
                                    'fieldname' => 't3ce_example_file',
                                ],
                                'foreign_label' => 'uid_local',
                                'foreign_selector' => 'uid_local',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_category' => [
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.categories',
                            'config' => [
                                'type' => 'category',
                                'foreign_table' => 'sys_category',
                                'size' => 20,
                                'foreign_table_where' => ' AND {#sys_category}.{#sys_language_uid} IN (-1, 0)',
                                'relationship' => 'manyToMany',
                                'maxitems' => 99999,
                                'default' => 0,
                                'MM' => 'sys_category_record_mm',
                                'MM_opposite_field' => 'items',
                                'MM_match_fields' => [
                                    'tablenames' => 'tt_content',
                                    'fieldname' => 't3ce_example_category',
                                ],
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_datetime' => [
                            'config' => [
                                'type' => 'datetime',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_select' => [
                            'config' => [
                                'type' => 'select',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_link' => [
                            'config' => [
                                'type' => 'link',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_radio' => [
                            'config' => [
                                'type' => 'radio',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_reference' => [
                            'config' => [
                                'type' => 'group',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_collection' => [
                            'config' => [
                                'type' => 'inline',
                                'foreign_table' => 't3ce_example_collection',
                                'foreign_field' => 'foreign_table_parent_uid',
                                'foreign_table_field' => 'tablenames',
                                'foreign_match_fields' => [
                                    'fieldname' => 't3ce_example_collection',
                                ],
                            ],
                            'exclude' => true,
                        ],
                        't3ce_testblock_text' => [
                            'config' => [
                                'type' => 'input',
                            ],
                            'exclude' => true,
                        ],
                        'bodytext' => [
                            'config' => [
                                'search' => [
                                    'andWhere' => '{#CType}=\'text\' OR {#CType}=\'textpic\' OR {#CType}=\'textmedia\' OR {#CType}=\'t3ce_example\' OR {#CType}=\'t3ce_testblock\'',
                                ],
                            ],
                        ],
                    ],
                    'palettes' => [
                        't3ce_example_palette_1' => [
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:palettes.palette_1.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:palettes.palette_1.description',
                            'showitem' => 't3ce_example_textarea,--linebreak--,t3ce_example_number',
                        ],
                    ],
                ],
                't3ce_example_collection' => [
                    'ctrl' => [
                        'title' => 't3ce_example_collection',
                        'label' => 'text2',
                        'sortby' => 'sorting',
                        'tstamp' => 'tstamp',
                        'crdate' => 'crdate',
                        'delete' => 'deleted',
                        'editlock' => 'editlock',
                        'versioningWS' => true,
                        'origUid' => 't3_origuid',
                        'hideTable' => true,
                        'transOrigPointerField' => 'l10n_parent',
                        'translationSource' => 'l10n_source',
                        'transOrigDiffSourceField' => 'l10n_diffsource',
                        'languageField' => 'sys_language_uid',
                        'enablecolumns' => [
                            'disabled' => 'hidden',
                            'starttime' => 'starttime',
                            'endtime' => 'endtime',
                            'fe_group' => 'fe_group',
                        ],
                        'typeicon_classes' => [
                            'default' => 'content-blocks',
                        ],
                        'security' => [
                            'ignorePageTypeRestriction' => true,
                        ],
                        'searchFields' => 'text,text2,palette_field1,palette_field2',
                    ],
                    'types' => [
                        '1' => [
                            'showitem' => 'text,--div--;LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.tabs.tab_1,text2,--palette--;;palette_inline,collection2,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access',
                        ],
                    ],
                    'palettes' => [
                        'language' => [
                            'showitem' => 'sys_language_uid,l18n_parent',
                        ],
                        'hidden' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                            'showitem' => 'hidden',
                        ],
                        'access' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,--linebreak--,fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,--linebreak--,editlock',
                        ],
                        'palette_inline' => [
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palettes.palette_inline.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palettes.palette_inline.description',
                            'showitem' => 'palette_field1,--linebreak--,palette_field2',
                        ],
                    ],
                    'columns' => [
                        'editlock' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'hidden' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'fe_group' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectMultipleSideBySide',
                                'size' => 5,
                                'maxitems' => 20,
                                'items' => [
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                                        'value' => -1,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                                        'value' => -2,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                                        'value' => '--div--',
                                    ],
                                ],
                                'exclusiveKeys' => '-1,-2',
                                'foreign_table' => 'fe_groups',
                            ],
                        ],
                        'starttime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'endtime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                                'range' => [
                                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                                ],
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'sys_language_uid' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                            'config' => [
                                'type' => 'language',
                            ],
                        ],
                        'l10n_parent' => [
                            'displayCond' => 'FIELD:sys_language_uid:>:0',
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    [
                                        'label' => '',
                                        'value' => 0,
                                    ],
                                ],
                                'foreign_table' => 't3ce_example_collection',
                                'foreign_table_where' => 'AND t3ce_example_collection.pid=###CURRENT_PID### AND t3ce_example_collection.sys_language_uid IN (-1,0)',
                                'default' => 0,
                            ],
                        ],
                        'l10n_diffsource' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'sorting' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'foreign_table_parent_uid' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'text' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'text2' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text2.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text2.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'palette_field1' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palette_field1.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palette_field1.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'palette_field2' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palette_field2.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.palette_field2.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'collection2' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.description',
                            'config' => [
                                'type' => 'inline',
                                'foreign_table' => 't3ce_example_collection2',
                                'foreign_field' => 'foreign_table_parent_uid',
                                'foreign_match_fields' => [
                                    'fieldname' => 't3ce_example_collection2',
                                ],
                                'foreign_table_field' => 'tablenames',
                            ],
                        ],
                    ],
                ],
                't3ce_example_collection2' => [
                    'ctrl' => [
                        'title' => 't3ce_example_collection2',
                        'label' => 'text',
                        'sortby' => 'sorting',
                        'tstamp' => 'tstamp',
                        'crdate' => 'crdate',
                        'delete' => 'deleted',
                        'editlock' => 'editlock',
                        'versioningWS' => true,
                        'origUid' => 't3_origuid',
                        'hideTable' => true,
                        'transOrigPointerField' => 'l10n_parent',
                        'translationSource' => 'l10n_source',
                        'transOrigDiffSourceField' => 'l10n_diffsource',
                        'languageField' => 'sys_language_uid',
                        'enablecolumns' => [
                            'disabled' => 'hidden',
                            'starttime' => 'starttime',
                            'endtime' => 'endtime',
                            'fe_group' => 'fe_group',
                        ],
                        'typeicon_classes' => [
                            'default' => 'content-blocks',
                        ],
                        'security' => [
                            'ignorePageTypeRestriction' => true,
                        ],
                        'searchFields' => 'text,text2',
                    ],
                    'types' => [
                        '1' => [
                            'showitem' => 'text,text2,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access',
                        ],
                    ],
                    'palettes' => [
                        'language' => [
                            'showitem' => 'sys_language_uid,l18n_parent',
                        ],
                        'hidden' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                            'showitem' => 'hidden',
                        ],
                        'access' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,--linebreak--,fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,--linebreak--,editlock',
                        ],
                    ],
                    'columns' => [
                        'editlock' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'hidden' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'fe_group' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectMultipleSideBySide',
                                'size' => 5,
                                'maxitems' => 20,
                                'items' => [
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                                        'value' => -1,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                                        'value' => -2,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                                        'value' => '--div--',
                                    ],
                                ],
                                'exclusiveKeys' => '-1,-2',
                                'foreign_table' => 'fe_groups',
                            ],
                        ],
                        'starttime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'endtime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                                'range' => [
                                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                                ],
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'sys_language_uid' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                            'config' => [
                                'type' => 'language',
                            ],
                        ],
                        'l10n_parent' => [
                            'displayCond' => 'FIELD:sys_language_uid:>:0',
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    [
                                        'label' => '',
                                        'value' => 0,
                                    ],
                                ],
                                'foreign_table' => 't3ce_example_collection2',
                                'foreign_table_where' => 'AND t3ce_example_collection2.pid=###CURRENT_PID### AND t3ce_example_collection2.sys_language_uid IN (-1,0)',
                                'default' => 0,
                            ],
                        ],
                        'l10n_diffsource' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'sorting' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'foreign_table_parent_uid' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'text' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.text.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'text2' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.text2.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.collection2.text2.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                    ],
                ],
                'sys_category' => [
                    'columns' => [
                        'items' => [
                            'config' => [
                                'MM_oppositeUsage' => [
                                    'tt_content' => [
                                        't3ce_example_category',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Content Block creating a new custom root table (not tt_content, generic content type)' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'path' => 'EXT:foo/ContentBlocks/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'table' => 'foobar',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'foobar' => [
                    'ctrl' => [
                        'title' => 'foobar',
                        'label' => 't3ce_example_text',
                        'sortby' => 'sorting',
                        'tstamp' => 'tstamp',
                        'crdate' => 'crdate',
                        'delete' => 'deleted',
                        'editlock' => 'editlock',
                        'versioningWS' => true,
                        'origUid' => 't3_origuid',
                        'hideTable' => false,
                        'transOrigPointerField' => 'l10n_parent',
                        'translationSource' => 'l10n_source',
                        'transOrigDiffSourceField' => 'l10n_diffsource',
                        'languageField' => 'sys_language_uid',
                        'enablecolumns' => [
                            'disabled' => 'hidden',
                            'starttime' => 'starttime',
                            'endtime' => 'endtime',
                            'fe_group' => 'fe_group',
                        ],
                        'typeicon_classes' => [
                            'default' => 'content-blocks',
                        ],
                        'security' => [
                            'ignorePageTypeRestriction' => true,
                        ],
                        'searchFields' => 't3ce_example_text',
                    ],
                    'types' => [
                        '1' => [
                            'showitem' => 't3ce_example_text,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access',
                        ],
                    ],
                    'palettes' => [
                        'language' => [
                            'showitem' => 'sys_language_uid,l18n_parent',
                        ],
                        'hidden' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                            'showitem' => 'hidden',
                        ],
                        'access' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,--linebreak--,fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,--linebreak--,editlock',
                        ],
                    ],
                    'columns' => [
                        'editlock' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'hidden' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'fe_group' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectMultipleSideBySide',
                                'size' => 5,
                                'maxitems' => 20,
                                'items' => [
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                                        'value' => -1,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                                        'value' => -2,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                                        'value' => '--div--',
                                    ],
                                ],
                                'exclusiveKeys' => '-1,-2',
                                'foreign_table' => 'fe_groups',
                            ],
                        ],
                        'starttime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'endtime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                                'range' => [
                                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                                ],
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'sys_language_uid' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                            'config' => [
                                'type' => 'language',
                            ],
                        ],
                        'l10n_parent' => [
                            'displayCond' => 'FIELD:sys_language_uid:>:0',
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    [
                                        'label' => '',
                                        'value' => 0,
                                    ],
                                ],
                                'foreign_table' => 'foobar',
                                'foreign_table_where' => 'AND foobar.pid=###CURRENT_PID### AND foobar.sys_language_uid IN (-1,0)',
                                'default' => 0,
                            ],
                        ],
                        'l10n_diffsource' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'sorting' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        't3ce_example_text' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'prefixing can be disabled globally' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'path' => 'EXT:foo/ContentBlocks/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'prefixFields' => false,
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Textarea',
                            ],
                            [
                                'identifier' => 'palette',
                                'type' => 'Palette',
                                'fields' => [
                                    [
                                        'identifier' => 'color',
                                        'type' => 'Color',
                                    ],
                                ],
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'tt_content' => [
                    'ctrl' => [
                        'typeicon_classes' => [
                            't3ce_example' => 't3ce_example-icon',
                        ],
                        'searchFields' => 'header,header_link,subheader,bodytext,pi_flexform,text',
                    ],
                    'types' => [
                        't3ce_example' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,text,--palette--;;palette,collection,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'text' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:text.description',
                                    'config' => [],
                                ],
                                'color' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:color.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:color.description',
                                    'config' => [],
                                ],
                                'collection' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                    ],
                    'columns' => [
                        'text' => [
                            'config' => [
                                'type' => 'text',
                            ],
                            'exclude' => true,
                        ],
                        'collection' => [
                            'config' => [
                                'type' => 'inline',
                                'foreign_table' => 'collection',
                                'foreign_table_field' => 'tablenames',
                                'foreign_match_fields' => [
                                    'fieldname' => 'collection',
                                ],
                                'foreign_field' => 'foreign_table_parent_uid',
                            ],
                            'exclude' => true,
                        ],
                        'color' => [
                            'config' => [
                                'type' => 'color',
                            ],
                            'exclude' => true,
                        ],
                    ],
                    'palettes' => [
                        'palette' => [
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:palettes.palette.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:palettes.palette.description',
                            'showitem' => 'color',
                        ],
                    ],
                ],
                'collection' => [
                    'ctrl' => [
                        'title' => 'collection',
                        'label' => 'text',
                        'sortby' => 'sorting',
                        'tstamp' => 'tstamp',
                        'crdate' => 'crdate',
                        'delete' => 'deleted',
                        'editlock' => 'editlock',
                        'versioningWS' => true,
                        'origUid' => 't3_origuid',
                        'hideTable' => true,
                        'transOrigPointerField' => 'l10n_parent',
                        'translationSource' => 'l10n_source',
                        'transOrigDiffSourceField' => 'l10n_diffsource',
                        'languageField' => 'sys_language_uid',
                        'enablecolumns' => [
                            'disabled' => 'hidden',
                            'starttime' => 'starttime',
                            'endtime' => 'endtime',
                            'fe_group' => 'fe_group',
                        ],
                        'typeicon_classes' => [
                            'default' => 'content-blocks',
                        ],
                        'security' => [
                            'ignorePageTypeRestriction' => true,
                        ],
                        'searchFields' => 'text',
                    ],
                    'types' => [
                        '1' => [
                            'showitem' => 'text,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access',
                        ],
                    ],
                    'palettes' => [
                        'language' => [
                            'showitem' => 'sys_language_uid,l18n_parent',
                        ],
                        'hidden' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                            'showitem' => 'hidden',
                        ],
                        'access' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,--linebreak--,fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,--linebreak--,editlock',
                        ],
                    ],
                    'columns' => [
                        'editlock' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'hidden' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'fe_group' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectMultipleSideBySide',
                                'size' => 5,
                                'maxitems' => 20,
                                'items' => [
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                                        'value' => -1,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                                        'value' => -2,
                                    ],
                                    [
                                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                                        'value' => '--div--',
                                    ],
                                ],
                                'exclusiveKeys' => '-1,-2',
                                'foreign_table' => 'fe_groups',
                            ],
                        ],
                        'starttime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'endtime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                                'range' => [
                                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                                ],
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'sys_language_uid' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                            'config' => [
                                'type' => 'language',
                            ],
                        ],
                        'l10n_parent' => [
                            'displayCond' => 'FIELD:sys_language_uid:>:0',
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    [
                                        'label' => '',
                                        'value' => 0,
                                    ],
                                ],
                                'foreign_table' => 'collection',
                                'foreign_table_where' => 'AND collection.pid=###CURRENT_PID### AND collection.sys_language_uid IN (-1,0)',
                                'default' => 0,
                            ],
                        ],
                        'l10n_diffsource' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'sorting' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'text' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text.label',
                            'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:collection.text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                        'foreign_table_parent_uid' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider checkTcaFieldTypesDataProvider
     */
    public function checkTcaFieldTypes(array $contentBlocks, array $expected): void
    {
        $GLOBALS['TCA']['tt_content']['ctrl']['type'] = 'CType';
        $GLOBALS['TCA']['tt_content']['columns']['bodytext'] = [
            'label' => 'Core bodytext field',
            'config' => [
                'type' => 'text',
                'search' => [
                    'andWhere' => '{#CType}=\'text\' OR {#CType}=\'textpic\' OR {#CType}=\'textmedia\'',
                ],
            ],
        ];
        $GLOBALS['TCA']['tt_content']['ctrl']['searchFields'] = 'header,header_link,subheader,bodytext,pi_flexform';

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
        $loader = new TestLoader($tableDefinitionCollection);
        $tcaGenerator = new TcaGenerator($loader, new NoopEventDispatcher(), new ContentBlockRegistry());

        $tca = $tcaGenerator->generate($tableDefinitionCollection);

        self::assertEquals($expected, $tca);
    }

    public static function checkFlexFormTcaDataProvider(): iterable
    {
        yield 'Two content blocks sharing a new flex form field by disabling prefixes' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'path' => 'EXT:foo/ContentBlocks/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'flex',
                                'type' => 'FlexForm',
                                'prefixField' => false,
                                'fields' => [
                                    [
                                        'identifier' => 'header',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'textarea',
                                        'type' => 'Textarea',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 't3ce/testblock',
                    'path' => 'EXT:foo/ContentBlocks/testblock',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'flex',
                                'type' => 'FlexForm',
                                'prefixField' => false,
                                'fields' => [
                                    [
                                        'identifier' => 'color',
                                        'type' => 'Color',
                                    ],
                                    [
                                        'identifier' => 'link',
                                        'type' => 'Link',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'tt_content' => [
                    'ctrl' => [
                        'typeicon_classes' => [
                            't3ce_example' => 't3ce_example-icon',
                            't3ce_testblock' => 't3ce_testblock-icon',
                        ],
                        'searchFields' => 'header,header_link,subheader,bodytext,pi_flexform',
                    ],
                    'types' => [
                        't3ce_example' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,flex,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'flex' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                        't3ce_testblock' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,flex,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'flex' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                    ],
                    'columns' => [
                        'flex' => [
                            'config' => [
                                'type' => 'flex',
                                'ds_pointerField' => 'CType',
                                'ds' => [
                                    't3ce_example' => '<T3FlexForms>
    <sheets type="array">
        <sDEF type="array">
            <ROOT type="array">
                <sheetTitle>Content Blocks Standard Sheet</sheetTitle>
                <type>array</type>
                <el type="array">
                    <header type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.header.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.header.description</description>
                        <config type="array">
                            <type>input</type>
                        </config>
                    </header>
                    <textarea type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.textarea.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:flex.textarea.description</description>
                        <config type="array">
                            <type>text</type>
                        </config>
                    </textarea>
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3FlexForms>',
                                    't3ce_testblock' => '<T3FlexForms>
    <sheets type="array">
        <sDEF type="array">
            <ROOT type="array">
                <sheetTitle>Content Blocks Standard Sheet</sheetTitle>
                <type>array</type>
                <el type="array">
                    <color type="array">
                        <label>LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.color.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.color.description</description>
                        <config type="array">
                            <type>color</type>
                        </config>
                    </color>
                    <link type="array">
                        <label>LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.link.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/testblock/Source/Language/Labels.xlf:flex.link.description</description>
                        <config type="array">
                            <type>link</type>
                        </config>
                    </link>
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3FlexForms>',
                                ],
                            ],
                            'exclude' => true,
                        ],
                    ],
                ],
            ],
        ];

        yield 'reusing existing flexForm field' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'path' => 'EXT:foo/ContentBlocks/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'pi_flexform',
                                'useExistingField' => true,
                                'fields' => [
                                    [
                                        'type' => 'Text',
                                        'identifier' => 'header',
                                    ],
                                    [
                                        'type' => 'Textarea',
                                        'identifier' => 'textarea',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 't3ce/example2',
                    'path' => 'EXT:foo/ContentBlocks/example2',
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'pi_flexform',
                                'useExistingField' => true,
                                'fields' => [
                                    [
                                        'type' => 'Text',
                                        'identifier' => 'header',
                                    ],
                                    [
                                        'type' => 'Textarea',
                                        'identifier' => 'textarea',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'tt_content' => [
                    'ctrl' => [
                        'typeicon_classes' => [
                            't3ce_example' => 't3ce_example-icon',
                            't3ce_example2' => 't3ce_example2-icon',
                        ],
                        'searchFields' => 'header,header_link,subheader,bodytext,pi_flexform',
                    ],
                    'types' => [
                        't3ce_example' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,pi_flexform,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'pi_flexform' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                        't3ce_example2' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,pi_flexform,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                            'columnsOverrides' => [
                                'pi_flexform' => [
                                    'label' => 'LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.label',
                                    'description' => 'LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.description',
                                    'config' => [],
                                ],
                            ],
                        ],
                    ],
                    'columns' => [
                        'pi_flexform' => [
                            'config' => [
                                'ds' => [
                                    '*,t3ce_example' => '<T3FlexForms>
    <sheets type="array">
        <sDEF type="array">
            <ROOT type="array">
                <sheetTitle>Content Blocks Standard Sheet</sheetTitle>
                <type>array</type>
                <el type="array">
                    <header type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.header.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.header.description</description>
                        <config type="array">
                            <type>input</type>
                        </config>
                    </header>
                    <textarea type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.textarea.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example/Source/Language/Labels.xlf:pi_flexform.textarea.description</description>
                        <config type="array">
                            <type>text</type>
                        </config>
                    </textarea>
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3FlexForms>',
                                    '*,t3ce_example2' => '<T3FlexForms>
    <sheets type="array">
        <sDEF type="array">
            <ROOT type="array">
                <sheetTitle>Content Blocks Standard Sheet</sheetTitle>
                <type>array</type>
                <el type="array">
                    <header type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.header.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.header.description</description>
                        <config type="array">
                            <type>input</type>
                        </config>
                    </header>
                    <textarea type="array">
                        <label>LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.textarea.label</label>
                        <description>LLL:EXT:foo/ContentBlocks/example2/Source/Language/Labels.xlf:pi_flexform.textarea.description</description>
                        <config type="array">
                            <type>text</type>
                        </config>
                    </textarea>
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3FlexForms>',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider checkFlexFormTcaDataProvider
     */
    public function checkFlexFormTca(array $contentBlocks, array $expected): void
    {
        $GLOBALS['TCA']['tt_content']['ctrl']['type'] = 'CType';
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform'] = [
            'label' => 'FlexForm',
            'config' => [
                'type' => 'flex',
                'ds_pointerField' => 'list_type,CType',
                'ds' => [
                    'default' => '<T3DataStructure><!-- example --></T3DataStructure>',
                ],
            ],
        ];
        $GLOBALS['TCA']['tt_content']['ctrl']['searchFields'] = 'header,header_link,subheader,bodytext,pi_flexform';

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
        $loader = new TestLoader($tableDefinitionCollection);
        $tcaGenerator = new TcaGenerator($loader, new NoopEventDispatcher(), new ContentBlockRegistry());

        $tca = $tcaGenerator->generate($tableDefinitionCollection);

        self::assertEquals($expected, $tca);
    }
}
