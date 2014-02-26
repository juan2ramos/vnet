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
	
	/**
	 * Funcion que trae los foros de la carrera y/o tema filtrados
	 * - Acceso desde TemasController
	 * - Acceso desde ForosController
	 *
     * @version 1
     * @param Int $carreraId Id de la carrera
     * @param Int $temaId Id del tema por el que se quiere filtrar (valor default = 0)
     * @return Array Arreglo con foros que estan incluidos en el filtro
	 */
	public function ForosCarrerasTemas($carreraId, $temaId)
	{
		$auxWhere = '';
		if ($temaId) {
			$auxWhere = ' AND t.id ='.$temaId;
		}
		/**
		 * SELECT * FROM foros f
		 * INNER JOIN temas t ON t.id = f.tema_id
		 * INNER JOIN carreras c ON c.id = t.carrera_id
		 * LEFT JOIN comentarios com ON com.foro_id = f.id;;
		 */
		$dql = "SELECT f.id, f.foroTitulo, f.foroTexto, f.created, t.nombre AS temaNombre, COUNT(comment.id) AS countComent
				FROM vocationetBundle:Foros f
				JOIN f.tema t
				JOIN t.carrera c
				LEFT JOIN vocationetBundle:Comentarios comment WITH comment.foro = f.id
				WHERE c.id =:carreraId".$auxWhere."
				GROUP BY f.id";
		$em = $this->doctrine->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
	}
}
?>