<?php

// ARCHIVO PARA ENCAPSULAR CÓDIGO Y REUTILIZAR

namespace App\Service;

use App\Entity\Tarea;
use App\Repository\TareaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TareaManager {

    private $entityManager;
    // private $tareaRepository;
    private $validator;

    public function __construct(
        TareaRepository $tareaRepository, 
        ValidatorInterface $validator,
        ManagerRegistry $doctrine
        ){

        // getManager() obtiene el objeto administrador de entidades de Doctrine,
        // que es el objeto más importante de Doctrine,
        // responsable de guardar y recuperar objetos de la base de datos.
        
        $this->entityManager    = $doctrine->getManager();
        $this->tareaRepository  = $tareaRepository;
        $this->validator        = $validator;
    }

    public function crear(Tarea $tarea){
        // guarda la tarea
        $this->entityManager->persist($tarea);
        // ejecuta un INSERT
        $this->entityManager->flush();
    }

    public function editar(Tarea $tarea):void   {
        $this->entityManager->flush();
    }

    public function eliminar(Tarea $tarea):void {
        
        $this->entityManager->remove($tarea);
        $this->entityManager->flush();
    }

    // comprueba que toda la información obligatoria estén rellenas 
    public function validar(Tarea $tarea): ConstraintViolationList
    {
        $errores = $this->validator->validate($tarea);
        /*if (empty($tarea->getDescripcion()))
            $errores[] = "Campo 'descripción' obligatorio";
        $tareaCondescripcionIgual = $this->tareaRepository->buscarTareaPorDescripcion($tarea->getDescripcion());
        if (null !== $tareaCondescripcionIgual && $tarea->getId() !== $tareaCondescripcionIgual->getId()) {
            $errores[] = "Descripción repetida";
        }*/
        
        return $errores;
    }

}