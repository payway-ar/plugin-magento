<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request;

use Prisma\Decidir\Gateway\Helper\DataReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Retrieves the token value from Additional Information
 */
class CredentialsDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */

    const TOKEN = 'token';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * @param DataReader $reader
     */
    public function __construct(DataReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $result = [];
        $paymentDO = $this->reader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $data = $payment->getAdditionalInformation();

        if (!empty($data[self::TOKEN])) {
            $result[self::TOKEN] = $data[self::TOKEN];
        }

        return $result;
    }
}
