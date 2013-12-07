<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para funciones relacionadas con foros, temas, carreras
 * @author Camilo Quijano <camilo@altactic.com>
 */
class ForosService
{
    var $doctrine;
        
    function __construct($doctrine) 
    {
        $this->doctrine = $doctrine;
    }
	
	/**
	 * Función que retorna el listado de temas de una carrera y cantidad de foros por tema
	 * - Acceso desde CarrerasController
	 * - Acceso desde ForosController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $carreraId Id de la carrera
     * @return Array Arreglo con datos del tema, y cantidad de foros
	 */
    public function getTemasCountForos($carreraId)
    {
		$dql = "SELECT t.id, t.nombre, COUNT(f.id) as cantidadForos
				FROM vocationetBundle:Temas t
				LEFT JOIN vocationetBundle:Foros f WITH f.tema = t.id
				WHERE t.carrera =:carreraId
				GROUP BY t.id";
		$em = $this->doctrine->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
	}
}
?>