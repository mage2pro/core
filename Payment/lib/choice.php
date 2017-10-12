<?php
use Df\Payment\Choice;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;

/**
 * 2017-04-17
 * @used-by \Df\Payment\Block\Info::choice()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @param II|OP|QP|O|Q|T $op
 * @return Choice
 */
function dfp_choice($op) {return Choice::f($op);}