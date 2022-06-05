<?php
// Archivo de ejecución para validar
namespace App\Validator;

use App\Repository\TareaRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TareaUnicaValidator extends ConstraintValidator
{

    private $tareaRepository;

    // accedemos al repositorio de las tareas para buscarlas
    // en el constructor del servicio inyectamos otro servicio
    public function __construct(TareaRepository $tareaRepository){
        $this->tareaRepository = $tareaRepository;
    }

    public function validate($tarea, Constraint $constraint)
    {
        // recuperamos la descripción que es lo que vamos a validar
        $descripcion = $tarea->getDescripcion();

        if (null === $descripcion || '' === $descripcion) {
            return;
        }
        // buscar tarea igual, si la encuentra devolvería un error
        // método creado en el repositorio de las tareas
        $tareaConDescripcionExistente = $this->tareaRepository->buscarTareaPorDescripcion($descripcion);
        
        // como vamos a editar, también debemos validar por id
        if (null !== $tareaConDescripcionExistente && $tarea->getId() !== $tareaConDescripcionExistente->getId())
        {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $descripcion)
            ->addViolation();
        }
        
    }
}
