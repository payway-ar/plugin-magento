<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Provides a description for this
 */
class DescriptionDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */

    const DESCRIPTION = 'description';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        return [
            self::DESCRIPTION => ''
        ];
    }
}
