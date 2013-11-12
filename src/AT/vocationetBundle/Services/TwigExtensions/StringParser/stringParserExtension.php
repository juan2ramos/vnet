<?php
namespace AT\vocationetBundle\Services\TwigExtensions\StringParser;

class stringParserExtension extends \Twig_Extension
{
    public function getFunctions() {
        return array(
            'explode' => new \Twig_Function_Method($this, 'explode'),
            'implode' => new \Twig_Function_Method($this, 'implode'),
            'strlen' => new \Twig_Function_Method($this, 'strLength'),
            'countArray' => new \Twig_Function_Method($this, 'countArray'),
        );
    }
    
    public function getFilters() {
        return array(
            'strip_tags' => new \Twig_Filter_Method($this, 'strip_tags_content'),
            'summary' => new \Twig_Filter_Method($this, 'resumirTexto'),
            'number_format' => new \Twig_Filter_Method($this, 'numberFormat'),
            'bytes_parser' => new \Twig_Filter_Method($this, 'bytesParser'),
        );
    }

    // METODOS
    
    /**
     * Extension de la funcion explode php para una funcion twig
     * 
     * @author Diego Malagón
     * @param string $delimiter
     * @param string $string
     * @param integer $limit
     * @return array
     */
    public function explode($delimiter, $string, $limit = false)
    {
        $return = ($limit) ? explode($delimiter, $string, $limit) : explode($delimiter, $string);        
        return array_filter($return);
    }
    
    /**
     * Extension de la funcion implode de php para una funcion twig
     * 
     * @author Diego Malagón
     * @param string $glue
     * @param array $pieces
     * @return string
     */
    public function implode($glue, $pieces)
    {
        return implode($glue, $pieces);
    }
    
    /**
     * Extension de la funcion count de php para una funcion twig
     * 
     * @author Diego Malagón
     * @param arrat $array
     * @return integer
     */
    public function countArray($array)
    {
        return count($array);
    }
    
    /**
     * Extension de la funcion strlen de php para una funcion twig
     * 
     * @author Diego Malagón
     * @param string $string
     * @return integer
     */
    public function strLength($string)
    {
        return strlen($string);
    }
    
    // FILTROS
     
    /**
     * Funcion que resume texto
     * 
     * @author Diego Malagón
     * @param string $string text
     * @param integer $limit numero de caracteres maximo
     * @param string $break caracter donde se puede romper el texto
     * @param string $pad cadena agregada al texto resumido
     * @return string
     */
    function resumirTexto($string, $limit, $break=" ", $pad="...") 
	{
		if(strlen($string) <= $limit)
		{
			$return = $string;
		}
		elseif(false !== ($breakpoint = strpos($string, $break, $limit))) 
		{
			if($breakpoint < strlen($string) - 1) 
			{
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}
		$return = $string;
		return $return;
	}

    /**
     * Funcion que elimina tags html de un texto
     * 
     * @author Diego Malagón
     * @param string $text texto
     * @param string $tags tags html permitidos o que deben ser eliminados
     * @param bool $invert false: elimina todos los tags exepto los que se encuentran en $tags, true: elimina unicamente los tags que estan en $tags
     * @return string
     */
    function strip_tags_content($text, $tags = '', $invert = false) 
    { 
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
        $tags = array_unique($tags[1]); 

        if(is_array($tags) AND count($tags) > 0) 
        { 
            if($invert == FALSE) 
            { 
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
            } 
            else 
            { 
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
            } 
        } 
        elseif($invert == FALSE) 
        { 
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
        } 
        return $text; 
    }     
    
    /**
     * Extension de la funcion number_format de php para un filtro twig
     * 
     * Adicionalmente, si se pasa por $decimals algun valor para que muestre la parte decimal de un numero
     * se muestra solamente si tiene parte decimal
     * 
     * @author Diego Malagón
     * @param float $number
     * @param integer $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string numero formateado
     */
    function numberFormat($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
    {
        $format = number_format($number, $decimals, $dec_point, $thousands_sep);
        if($decimals > 0)
        {
            $exp = explode($dec_point, $format);
            if($exp[1] == 0)
            {
                $format = $exp[0];
            }
        }
        return $format;
    }
    
    /**
     * Funcion que convierte bytes a unidades de KB, MB o GB
     * 
     * @author Diego Malagón
     * @param integer $bytes bytes
     * @return string peso con formato
     */
    function bytesParser($bytes)  
    { 
		$size = $bytes / 1024; 
		if($size < 1024) 
		{ 
			$parse = $size; 
			$u = ' KB'; 
		}  
		else  
		{ 
			if($size / 1024 < 1024)  
			{ 
				$parse = $size / 1024; 
				$u = ' MB'; 
			}  
			else if ($size / 1024 / 1024 < 1024)   
			{ 
				$parse = $size / 1024 / 1024; 
				$u = ' GB'; 
			}  
		} 
        $size = $this->numberFormat($parse, 2).$u; 
		return $size; 
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'stringParserExtension';
    }
}
?>
