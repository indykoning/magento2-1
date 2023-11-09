<?php
/************************************************************************
 *
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ************************************************************************
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model\Cart;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAlreadyExistsException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

/**
 * Validates a pre-defined masked quote id
 */
class ValidateMaskedQuoteId
{
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;

    /**
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     */
    public function __construct(
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    /**
     * Validate masked id
     *
     * @param string $maskedId
     * @throws GraphQlAlreadyExistsException
     * @throws GraphQlInputException
     */
    public function execute(string $maskedId): void
    {
        if (mb_strlen($maskedId) != 32) {
            throw new GraphQlInputException(__('Cart ID length should to be 32 symbols.'));
        }

        if ($this->isQuoteWithSuchMaskedIdAlreadyExists($maskedId)) {
            throw new GraphQlAlreadyExistsException(__('Cart with ID "%1" already exists.', $maskedId));
        }
    }

    /**
     * Check is quote with such maskedId already exists
     *
     * @param string $maskedId
     * @return bool
     */
    private function isQuoteWithSuchMaskedIdAlreadyExists(string $maskedId): bool
    {
        try {
            $this->maskedQuoteIdToQuoteId->execute($maskedId);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
