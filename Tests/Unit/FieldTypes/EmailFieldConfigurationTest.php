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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\FieldTypes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\FieldType\EmailFieldType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EmailFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'non_available_field' => 'foo',
                'default' => 'Default value',
                'placeholder' => 'Placeholder text',
                'autocomplete' => 1,
                'required' => 1,
                'readOnly' => 1,
                'nullable' => 1,
                'mode' => 'useOrOverridePlaceholder',
                'eval' => ['trim', 'lower'],
            ],
            'expectedTca' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'config' => [
                    'type' => 'email',
                    'default' => 'Default value',
                    'readOnly' => true,
                    'nullable' => true,
                    'mode' => 'useOrOverridePlaceholder',
                    'placeholder' => 'Placeholder text',
                    'required' => true,
                    'eval' => 'trim,lower',
                    'autocomplete' => true,
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'label' => '',
                'description' => null,
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
                'exclude' => false,
                'non_available_field' => 'foo',
                'default' => '',
                'placeholder' => '',
                'size' => 0,
                'autocomplete' => 0,
                'required' => 0,
                'readOnly' => 0,
                'nullable' => 0,
                'mode' => '',
                'eval' => [],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'email',
                    'autocomplete' => false,
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = EmailFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` VARCHAR(255) DEFAULT \'\' NOT NULL',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = EmailFieldType::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
