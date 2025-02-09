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

namespace TYPO3\CMS\ContentBlocks\Schema\Field;

use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final readonly class TcaField implements TcaFieldTypeInterface
{
    public function __construct(
        private FieldTypeInterface $fieldType,
        private string $name,
        private array $columnConfig,
    ) {}

    public function getType(): FieldTypeInterface
    {
        return $this->fieldType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumnConfig(): array
    {
        return $this->columnConfig;
    }
}
