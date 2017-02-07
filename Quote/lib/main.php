<?php
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface as IQuoteRepository;
use Magento\Quote\Api\Data\CartInterface as IQuote;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
/**
 * 2016-07-18
 * @used-by \Df\Payment\ConfigProvider::config()
 * @param int|null $id [optional]
 * @return IQuote|Quote
 * @throws NoSuchEntityException
 */
function df_quote($id = null) {return $id ? df_quote_r()->get($id) : df_checkout_session()->getQuote();}

/**
 * 2016-07-18
 * @return IQuoteRepository|QuoteRepository
 */
function df_quote_r() {return df_o(IQuoteRepository::class);}

