<?php
use Magento\Payment\Helper\Data as H;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order\Payment\Repository;

/**
 * 2020-02-02
 * @used-by dfp_methods()
 * @return H
 */
function dfp_h():H {return df_o(H::class);}

/**
 * 2016-05-07 https://mage2.pro/tags/order-payment-repository
 * @used-by dfp()
 * @return IRepository|Repository
 */
function dfp_r():IRepository {return df_o(IRepository::class);}