<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
namespace Prisma\Decidir\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Fills out the address validation code
 *
 * It will only populate a value if VISA was used
 */
class AddressValidationHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const STATUS_DETAILS = 'status_details';

    /**
     * @var string
     */
    const ADDRESS_CODE = 'address_validation_code';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * AddressValidationHandler constructor.
     * @param DataReader $reader
     */
    public function __construct(
        DataReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $object = $this->reader->readPayment($handlingSubject);
        $paymentResponse = $this->reader->readTransaction($response);
        $data = $paymentResponse->getDataField();

        /** @var Payment $payment */
        $payment = $object->getPayment();

        $this->setAddressValidation($payment, $data);
    }

    /**
     * @param OrderPaymentInterface $payment
     * @param array $data
     */
    public function setAddressValidation(OrderPaymentInterface $payment, array $data)
    {
        // Check $data['status_details']['address_validation_code'] exists
        $validationCode = isset($data[self::STATUS_DETAILS])
            && isset($data[self::STATUS_DETAILS][self::ADDRESS_CODE])
                ? $data[self::STATUS_DETAILS][self::ADDRESS_CODE]
                : null;

        if ($validationCode) {
            $payment->setAddressStatus($validationCode);
        }
    }
}
