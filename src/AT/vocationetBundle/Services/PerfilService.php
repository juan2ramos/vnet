<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para el manejo de perfiles
 * @author Camilo Quijano <camilo@altactic.com>
 * @package Services
 */
class PerfilService
{
    var $doctrine;
    var $translate;
        
    function __construct($doctrine, $translate)
    {
        $this->doctrine = $doctrine;
        $this->translate = $translate;
    }

    /**
	 * Funcion que trae la información del usuario del perfil al que se quiere acceder
	 * incluido colegio al que pertenece de ser rol estudiante
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $perfilId Id del perfil al acceder
     * @return Array Arreglo con informacion del perfil
	 */
    public function getPerfil($perfilId)
    {
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los datos del perfil al que se quiere ingresar (mentor)
		 * SELECT u.id, p.id, r.nombre, u.usuario_nombre, u.usuario_apellido 
		 * FROM perfiles p
		 * JOIN usuarios u ON u.id = p.usuario_id
		 * JOIN roles r ON r.id = u.rol_id
		 * WHERE p.id = 2
		 * //AND (r.nombre = 'mentor_e' or r.nombre = 'mentor_ov');
		 */
		$dql="SELECT u.id as usuarioId, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen, u.usuarioFechaNacimiento, u.usuarioEmail,
				u.usuarioHojaVida, u.usuarioTarjetaProfesional, u.usuarioValorMentoria, u.usuarioPerfilProfesional,
				u.usuarioCursoActual, u.usuarioFacebookid, u.usuarioFechaPlaneacion,
				r.nombre as nombreRol,
				col.nombre as nombreCol, col.id as colegioId
			FROM vocationetBundle:Usuarios u
			JOIN u.rol r
			LEFT JOIN u.colegio col
			WHERE u.id =:perfilId";
		$query = $em->createQuery($dql);
		$query->setParameter('perfilId', $perfilId);
		$perfil =$query->getResult();
		return $perfil[0];
	}

	/**
	 * Funcion que trae los estudios registrados del perfil
	 * - Estudios de rol mentor vocacional o experto
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $perfilId Id del perfil al acceder
	 * @return Array Arreglo con estudios registrados del perfil
	 */
	public function getEstudiosPerfil($perfilId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los estudios del perfil que ingresa por Id
		 * SELECT * FROM estudios e
		 * INNER JOIN instituciones i ON e.institucion_id = i.id
		 * WHERE e.perfil_id = 2;
		 */
		 $dql= "SELECT est FROM vocationetBundle:Estudios est
			WHERE est.usuario =:perfilId";
			$query = $em->createQuery($dql);
		$query->setParameter('perfilId', $perfilId);
		$estudios =$query->getResult();
		return $estudios;
	}

	/**
	 * Funcion que trae los trabajos registrados del perfil
	 * - Trabajos del rol mentor vocacional o experto
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $perfilId Id del perfil al acceder
	 * @return Array Arreglo con trabajos registrados del perfil
	 */
	public function getTrabajosPerfil($perfilId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los trabajos del perfil que ingresa por Id
		 * SELECT * FROM trabajos tra
		 * INNER JOIN empresas emp ON tra.empresa_id = emp.id
		 * WHERE tra.perfil_id = 2;
		 */
		 $dql= "SELECT tra FROM vocationetBundle:Trabajos tra
			INNER JOIN tra.empresa emp
			WHERE tra.usuario =:perfilId";
			$query = $em->createQuery($dql);
		$query->setParameter('perfilId', $perfilId);
		$estudios =$query->getResult();
		return $estudios;
	}

	/**
	 * Funcion que retorna un array los colegios a seleccionar por el estudiante
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @return Array Con colegios (cursos)
	 */
	public function getColegios()
	{
		$arrayColegios = Array();
		$em = $this->doctrine->getManager();
		$colegios = $em->getRepository('vocationetBundle:colegios')->findAll();
		foreach($colegios as $colegio){
			$arrayColegios[$colegio->getId()] = $colegio->getNombre();
		}
		return $arrayColegios;
	}

	/**
	 * Funcion que retorna un array con los grados (cursos) a seleccionar por el estudiante
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @return Array Con grados (cursos)
	 */
	public function getGrados()
	{
		$tr = $this->translate;
		return $grados = Array(
					'6' => $tr->trans("sexto", array(), 'label'),
					'7' => $tr->trans("septimo", array(), 'label'),
					'8' => $tr->trans("octavo", array(), 'label'),
					'9' => $tr->trans("noveno", array(), 'label'),
					'10' => $tr->trans("decimo", array(), 'label'),
					'11' => $tr->trans("once", array(), 'label'),
					'12' => $tr->trans("doce", array(), 'label'),
				);
	}

	public function getRutaHojaVida()
	{
		return 'img/vocationet/usuarios/';
	}

	public function getRutaTarjetaProfesional()
	{
		return 'img/vocationet/usuarios/';
	}
}
?>
