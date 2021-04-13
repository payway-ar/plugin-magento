<?php
namespace Prisma\Decidir\Model;

class Decidir extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'decidir';

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'decidir';
}
