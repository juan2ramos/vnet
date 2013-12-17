<?php

namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de pagos con PayU
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class PayUService
{
    /**
     * Contenedor de servicios
     * 
     * @var Object 
     */
    protected $serv_cont;
    
    /**
     * URL web checkout PayU
     * 
     * @var string 
     */
    protected $webCheckoutUrl;
    
    /**
     * Identificador de comercio en el sistema de PayU
     * 
     * @var string 
     */
    protected $merchantId;
    
    /**
     * ApiKey de PayU
     * 
     * @var string 
     */
    protected $apiKey;
    
    /**
     * Identificador de la cuenta
     * 
     * @var string 
     */
    protected $accountId;

    
    /**
     * Constructor
     * 
     * @param Object $service_container contenedor de servicios
     * @param string $webCheckout URL web checkout PayU
     * @param string $merchantId Identificador de comercio en el sistema de PayU
     * @param string $apiKey ApiKey de PayU
     * @param string $accountId Identificador de la cuenta
     */
    function __construct($service_container, $webCheckoutUrl, $merchantId, $apiKey, $accountId) 
    {
        $this->serv_cont = $service_container;
        $this->webCheckoutUrl = $webCheckoutUrl;
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
    }
}

