<?php

namespace AT\vocationetBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Servicio para el control de archivo en la aplicacion
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class FileService
{
    var $doctrine;
    var $session;
    var $root;
    
    function __construct($doctrine, $session) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->root = '../uploads/';
    }
    
    /**
     * Funcion para cargar archivos a la aplicacion
     * 
     * @param array $file arreglo con objetos UploadFile del formlario
     * @param string $path ruta del directorio donde se cargara el archivo
     * @return array arreglo con los id en db de los archivos cargados
     */
    public function upload($files, $path)
    {
        $files_id = array();
        
        if(count($files) > 0)
        {        
            $em = $this->doctrine->getManager();        

            foreach($files as $file)
            {
                if(is_object($file))
                {
                    $size = $file->getClientSize();
                    $name = $file->getClientOriginalName();
                    $ext = $this->getExtension($name);
                    $pathName = uniqid('f', true).'.'.$ext;
                    $filePath = $path.$pathName;

                    // Mover archivo
                    $file->move($this->root.$path, $pathName);

                    // Registrar en la db
                    $archivo = new \AT\vocationetBundle\Entity\Archivos();

                    $archivo->setArchivoNombre($name);
                    $archivo->setArchivoPath($filePath);
                    $archivo->setArchivoSize($size);
                    $archivo->setCreated(new  \DateTime());

                    $em->persist($archivo);
                    $em->flush();
                    $files_id[] = $archivo->getId();
                }
            }

            
        }
        return $files_id;
    }
    
    /**
     * Funcion para descargar archivos
     * 
     * @param integer $id id de archivo a descargar
     * @return \Symfony\Component\HttpFoundation\Response response con forzamiento de descarga
     */
    public function download($id)
    {
        $em = $this->doctrine->getManager();
        
        $archivo = $em->getRepository('vocationetBundle:Archivos')->findOneById($id);
        
        $path = $this->root.$archivo->getArchivoPath();
        
        $response = new Response();
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archivo->getArchivoNombre());
        $response->headers->set('Content-Disposition', $d);
        $response->setContent(file_get_contents($path));
        
        return $response;
    }
    
    /**
     * Funcion que obtiene la extension de un archivo pasando el nombre completo
     * 
     * @param string $name nombre completo del archivo
     * @param string|boolean $type retorno de guessExtension() del objeto UploadFile
     * @return string extension del archivo
     */
    public function getExtension($name, $type=false)
    {
        $ext = false;
        
        $tmp_ext = explode('.',$name);
        if(count($tmp_ext)>1)
        {
            $ext = $tmp_ext[count($tmp_ext)-1];
        }
        elseif($type)
        {
            $ext = $type;
        }
        else
        {
            $ext = "bin";
        }
        
        return strtolower($ext);
    }
}
?>
