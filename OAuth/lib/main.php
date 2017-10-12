<?php
use Df\OAuth\App;
/**
 * 2017-07-10          
 * @used-by \Df\OAuth\FE\Button::app() 
 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::_execute()  
 * @used-by \Dfe\Dynamics365\API\Client::headers()
 * @used-by \Dfe\Salesforce\API\Client::headers()
 * @param string|object $c
 * @return App
 */
function df_oauth_app($c) {return dfs_con($c, 'OAuth\\App');}