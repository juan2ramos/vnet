<?php
namespace AT\vocationetBundle\Services;
use AT\vocationetBundle\Entity\Estudios;
use AT\vocationetBundle\Entity\Empresas;
use AT\vocationetBundle\Entity\Trabajos;

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
	 * @param Int $usuarioId Id del perfil al acceder
	 * @return Array Arreglo con estudios registrados del perfil
	 */
	public function getEstudiosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que elimina los estudios del usuario que ingresa por parametro
		 * SELECT * FROM estudios e
		 * WHERE e.usuario_id = 2;
		 */
		 $dql= "SELECT est FROM vocationetBundle:Estudios est
			WHERE est.usuario =:usuarioId";
			$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$estudios =$query->getResult();
		return $estudios;
	}
	
	/**
	 * Funcion que elimina los estudios registrados del usuario que ingresa por parametro
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del perfil a eliminar los estudios
	 */
	public function deleteEstudiosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los estudios del perfil que ingresa por Id
		 * DELETE FROM estudios e WHERE e.usuario_id = 2;
		*/
		$dql= "DELETE FROM vocationetBundle:Estudios est WHERE est.usuario =:usuarioId";
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$query->getResult();
	}

	/**
	 * Funcion que guarda los estudios de un usuario
	 * 
	 * Claves del array que ingresa por cada estudio
	 * $estudios[] = Array('id','institucion','notas','actividades','titulo','estudios', 'yearinicio','yearfin')
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del perfil a relacionar los estudios
	 * @param Array $estudios Arreglo con estudios a vincular al usuario.
	 */
	public function setEstudiosPerfil($estudios, $usuarioId)
	{
		$em = $this->doctrine->getManager();
		$Objusuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
		foreach($estudios as $estudio)
		{
			$newEstudio = new Estudios();
			$newEstudio->setNombreInstitucion($estudio['institucion']);
			$newEstudio->setUsuario($Objusuario);
			$newEstudio->setTitulo($estudio['titulo']);
			$newEstudio->setNotas($estudio['notas']);
			$newEstudio->setCampo($estudio['estudios']);
			$newEstudio->setActividad($estudio['actividades']);
			$newEstudio->setIdLinkedin($estudio['id']);
			if ($estudio['yearinicio']) {
				$newEstudio->setFechaInicio(new \DateTime($estudio['yearinicio'].'-01-01'));
			}
			if ($estudio['yearfin']) {
				$newEstudio->setFechaFinal(new \DateTime($estudio['yearfin'].'-01-01'));
			}
			$em->persist($newEstudio);
		}
		$em->flush();
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
	public function getTrabajosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los trabajos del perfil que ingresa por Id
		 * SELECT * FROM trabajos tra
		 * INNER JOIN empresas emp ON tra.empresa_id = emp.id
		 * WHERE tra.usuario_id = 2;
		 */
		 $dql= "SELECT tra FROM vocationetBundle:Trabajos tra
			INNER JOIN tra.empresa emp
			WHERE tra.usuario =:usuarioId";
			$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$estudios =$query->getResult();
		return $estudios;
	}

	/**
	 * Funcion que elimina los trabajos registrados del usuario que ingresa por parametro
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del usuario a eliminar los trabajos
	 */
	public function deleteTrabajosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que elimina los trabajos del usuario que ingresa por parametro
		 * DELETE FROM trabajos tra WHERE tra.usuario_id = 2;
		 */
		$dql= "DELETE FROM vocationetBundle:Trabajos tra WHERE tra.usuario =:usuarioId";
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$query->getResult();
	}

	/**
	 * Funcion que guarda los trabajos de un usuario
	 * 
	 * Claves del array que ingresa por cada trabajo
	 *	$ARRpositions[] = Array(
	 * 				'id' => "Id de linkedIn"
	 * 				'title' => "Cargo"
	 * 				'summary' => "Descripción"
	 * 				'isCurrent' => "Actualmente labora aqui"
	 * 				'startDateY' => "Año de inicio"
	 * 				'startDateM' => "Mes de inicio"
	 * 				'endDateY' =>"Año de finalización"
	 * 				'endDateM' => "Mes de finalización"
	 * 				//DATOS DE LA COMPAÑIA
	 * 				'companyId' => "Id de la compañia"
	 * 				'companyName' => "Nombre de la compañia"
	 * 				'companyType' => "Tipo de compañia"
	 *				'companySize' => "Tamaño de la compañia"
	 * 				'companyIndustry' => "Industria de la compañia"
	 * 	);
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del perfil a relacionar los trabajos
	 * @param Array $estudios Arreglo con trabajos a vincular al usuario.
	 */
	public function setTrabajosPerfil($trabajos, $usuarioId)
	{
		$em = $this->doctrine->getManager();
		$Objusuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
		
		foreach($trabajos as $trabajo)
		{
			$crearEmpresa = false;
			if ($trabajo['companyId']) {
				$ObjCompany = $em->getRepository('vocationetBundle:Empresas')->findOneByIdLinkedin($trabajo['companyId']);
				if (!$ObjCompany) {
					$crearEmpresa = true;
				}
			}
			else {
				$ObjCompany = $em->getRepository('vocationetBundle:Empresas')->findOneByNombre($trabajo['companyName']);
				if (!$ObjCompany) {
					$crearEmpresa = true;
				}
			}

			if ($crearEmpresa) {
				$ObjCompany = new Empresas();
			}

			$ObjCompany->setNombre($trabajo['companyName']);
			$ObjCompany->setTipo($trabajo['companyType']);
			$ObjCompany->setSize($trabajo['companySize']);
			$ObjCompany->setIndustria($trabajo['companyIndustry']);
			$ObjCompany->setIdLinkedin($trabajo['companyId']);
			$em->persist($ObjCompany);

			if ($crearEmpresa) {
				//$em->flush();
			}

			$newJob = new Trabajos();
			$newJob->setUsuario($Objusuario);
			$newJob->setEmpresa($ObjCompany);
			$newJob->setCargo($trabajo['title']);
			$newJob->setEsActual($trabajo['isCurrent']);
			$em->persist($newJob);
		}
		$em->flush();
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

	/**
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las hojas de vida
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return String Ruta de acceso a hojas de vida
	 */
	public function getRutaHojaVida()
	{
		return 'img/vocationet/usuarios/';
	}

	/**
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las tarjetas profesionales
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return String Ruta de acceso a tarjetas profesionales
	 */
	public function getRutaTarjetaProfesional()
	{
		return 'img/vocationet/usuarios/';
	}
}
?>
