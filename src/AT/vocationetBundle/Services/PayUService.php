<?php

namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de pagos a través de web checkout de PayU
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
     * Moneda por defecto
     * 
     * @var string 
     */
    protected $currency;
    
    /**
     * token para proteccion de csrf
     * 
     * @var string 
     */
    protected $token;
    
    /**
     * Activa el entorno de pruebas
     * 
     * @var integer 
     */
    protected $test;
    
    /**
     * Url al controlador de respuesta de PayU
     * 
     * @var string 
     */
    protected $responseUrl;
    
    /**
     * Url al controlador de confirmacion de PayU
     * 
     * @var string 
     */
    protected $confirmationUrl;
        
    /**
     * Constructor
     * 
     * @param Object $service_container contenedor de servicios
     * @param string $webCheckout URL web checkout PayU
     * @param string $merchantId Identificador de comercio en el sistema de PayU
     * @param string $apiKey ApiKey de PayU
     * @param string $accountId Identificador de la cuenta
     */
    function __construct($service_container, $webCheckoutUrl, $merchantId, $apiKey) 
    {
        $this->serv_cont = $service_container;
        $this->webCheckoutUrl = $webCheckoutUrl;
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
        $this->currency = 'COP';
        $this->token = '60d20bcbfad939a9126a34c124259889ec9ed22e';
        $this->test = 0;
        $this->responseUrl = $service_container->get('request')->getSchemeAndHttpHost().$service_container->get('router')->generate('payu_response');
        $this->confirmationUrl = $service_container->get('request')->getSchemeAndHttpHost().$service_container->get('router')->generate('payu_confirmation');
    }
    
    /**
     * Funcion que retorna los posibles estados de una transaccion
     * 
     * @return array
     */
    public function getTransactionStaues()
    {
        return array(
            4 => 'CAPTURING_DATA', // Capturando datos.
            2 => 'NEW', // Estado inicial de la transacción.
            101 => 'FX_CONVERTED', // Retornado por el conversor de monedas, indicando la modificación realizada.
            102 => 'VERIFIED', // Indica que la transacción fue evaluada por nuestro módulo antifraude.
            103 => 'SUBMITTED', // Movimiento fue enviado para su procesamiento al proveedor de pago.
            4 => 'APPROVED', // La transacción fue aprobada por la entidad financiera.
            6 => 'DECLINED', // Transacción Declinada o Abandonada.
            104 => 'ERROR', // Se presentó un error con el medio de pago externo.
            7 => 'PENDING', // Operación pendiente de finalización.
            5 => 'EXPIRED', // Transacción expiró, por superar el tiempo límite de respuesta.            
        );
    }
    
    /**
     * Funcion que retorna los posibles codigos de respuesta
     * 
     * @return array
     */
    public function getResponseCodes()
    {
        return array(
            1 => 'Transacción Aprobada.',
            4 => 'Transacción rechazada por la entidad.',
            5 => 'Transacción declinada por la entidad financiera.',
            6 => 'Fondos insuficientes.',
            7 => 'Tarjeta inválida.',
            8 => 'Es necesario contactar a la entidad.',
            9 => 'Tarjeta vencida.',
            10 => 'Tarjeta restringida.',
            12 => 'Fecha de expiración o campo seg. Inválidos.',
            13 => 'Repita transacción.',
            14 => 'Transacción inválida.',
            15 => 'Transacción enviada a Validación Manual.',
            17 => 'Monto excede máximo permitido por entidad.',
            22 => 'Tarjeta no autorizada para realizar compras por internet.',
            23 => 'Transacción Rechazada por el Modulo Antifraude.',
            50 => 'Transacción Expirada, antes de ser enviada a la red del medio de pago.',
            51 => 'Ocurrió un error en el procesamiento por parte de la Red del Medio de Pago.',
            52 => 'El medio de Pago no se encuentra Activo. No se envía la solicitud a la red del mismo.',
            53 => 'Banco no disponible.',
            54 => 'El proveedor del Medio de Pago notifica que no fue aceptada la transacción.',
            55 => 'Error convirtiendo el monto de la transacción.',
            56 => 'Error convirtiendo montos del deposito.',
            9994 => 'Transacción pendiente por confirmar.',
            9995 => 'Certificado digital no encontrado.',
            9997 => 'Error de mensajería con la entidad financiera.',
            10000 => 'Ajustado Automáticamente.',
            10001 => 'Ajuste Automático y Reversión Exitosa.',
            10002 => 'Ajuste Automático y Reversión Fallida.',
            10003 => 'Ajuste automático no soportado.',
            10004 => 'Error en el Ajuste.',
            10005 => 'Error en el ajuste y reversión.',
        );
    }
    
    /**
     * Funcion que retorna la url para realizar el web checkout
     * 
     * @return string
     */
    public function getWebCheckoutUrl()
    {
        return $this->webCheckoutUrl;
    }
    
    /**
     * Funcion que genera los parametros necesarios para enviar una peticion de pago a PayU
     * 
     * @param string $buyerEmail email del usuario
     * @param string $referenceCode codigo de la orden
     * @param float $amount valor total de la orden
     * @param float $tax valor del iva
     * @param float $taxReturnBase subtotal de la orden
     * @return array arreglo de parametros
     */
    public function generateDataFormWebCheckout($buyerEmail, $referenceCode, $amount, $tax, $taxReturnBase)
    {
        $signature = $this->generateSignature($referenceCode, $this->amountFormat($amount), $this->merchantId);        
        
        $data = array(
            'merchantId' => $this->merchantId,
            'referenceCode' => $referenceCode,
            'description' => $this->serv_cont->get('translator')->trans("compra.en.vocationet"),
            'amount' => $this->amountFormat($amount),
            'tax' => $this->amountFormat($tax),
            'taxReturnBase' => $this->amountFormat($taxReturnBase),
            'signature' => $signature,
            'currency' => $this->currency,
            'buyerEmail' => $buyerEmail,
            'test' => $this->test,
            'responseUrl' => $this->responseUrl,
            'confirmationUrl' => $this->confirmationUrl
        );
        
        return $data;
    }
    
    /**
     * Funcion para generar signature para una orden de compra
     * 
     * Genera el signature de acuerdo a lo solicitado por PayU
     * 
     * @param string $referenceCode referenceCode de la orden
     * @param string $amount valor total de la orden
     * @param string $merchantId identificador de comercio, no se hace referencia al atributo de la clase por las validaciones de seguridad
     * @return string signature en MD5
     */
    private function generateSignature($referenceCode, $amount, $merchantId)
    {
        $signature = $this->apiKey."~".$merchantId."~".$referenceCode."~".$amount."~".$this->currency;
        $md5 = md5($signature);
//        echo $signature."<br>";
//        echo $md5."<br>";        
        return $md5;
    }
    
    /**
     * Funcion para procesar la respuesta de PayU
     * 
     * la peticion es recibida a traves de GET
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return boolean|string retorna el codigo de la transaccion si fue exitosa, false en otro caso
     */
    public function processResponse($request)
    {
        $response = $request->query->all();        
        
        $amount = $this->amountFormat($response['TX_VALUE']);
        
        $signature = $this->generateSignature($response['referenceCode'], $amount, $response['merchantId']); 
        $responseSignature = $response['signature'];
//        $responseSignature = $signature;
        
        
//        SecurityService::debug($signature);
//        SecurityService::debug($responseSignature);
        
        
        $trans = $this->serv_cont->get('translator');
        $message = '
            <table class="table">
                <tbody>
                    <tr><td>'.$trans->trans("descripcion.transaccion").'</td><td>'.$response['description'].'</td></tr>
                    <tr><td>'.$trans->trans("estado.transaccion").'</td><td>'.$response['message'].'</td></tr>
                    <tr><td>'.$trans->trans("metodo.transaccion").'</td><td>'.$response['lapPaymentMethod'].'</td></tr>
                    <tr><td>'.$trans->trans("valor.transaccion").'</td><td>$ '.$amount.'</td></tr>
                </tbody>
            </table>
        ';        
        
        if($signature == $responseSignature)
        {
            if($response['transactionState'] == 4 && $response['polResponseCode'] == 1)
            {
                return array(
                    'status' => 'success',
                    'message' => $message,
                    'referenceCode' => $response['referenceCode']  
                );
            } 
            else
            {
                return array(
                    'status' => 'error',
                    'message' => $message,
                    'referenceCode' => $response['referenceCode']  
                );
            }
        }
        else
        {
            return array(
                'status' => 'error',
                'message' => $trans->trans("validacion.transaccion.invalida"),
                'referenceCode' => $response['referenceCode']
            );
        }
    }
    
    /**
     * Funcion para procesar la confirmacion de pago de PayU
     * 
     * la peticion es recibida a traves de POST
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return boolean|string retorna el codigo de la transaccion si fue exitosa, false en otro caso
     */
    public function processConfirmation($request)
    {
        $response = $request->request->all();
        
        $amount = $this->amountFormat($response['value']);
        
        $signature = $this->generateSignature($response['reference_sale'], $amount, $response['merchant_id']); 
        $responseSignature = $response['sign'];
//        $responseSignature = $signature;
        
        if($signature == $responseSignature)
        {
            if($response['state_pol'] == 4 && $response['response_code_pol'] == 1)
            {
                return array(
                    'referenceCode' => $response['reference_sale'],
                    'buyerEmail' => $response['email_buyer'],
                );
            }
        }
        
        return false;
    }
    
    
    /**
     * Funcion para formatear un valor numerico
     * 
     * @param float $amount valor
     * @return float valor formateado
     */
    private function amountFormat($amount)
    {
        return number_format($amount, 1, '.', '');
    }
}

