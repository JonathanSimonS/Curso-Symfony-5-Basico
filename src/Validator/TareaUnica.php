<?php
// Archivo definicion de la validación  
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TareaUnica extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Tarea con descripción "{{ value }}" ya existe.';

    // Comprobación a nivel de clase
    // en el $value de la funcion validate de TareaUnicaValidator.php no venga el nombre de la propiedad,
    // si no la entidad completa   en la clse poner:  * @AppAssert\TareaUnica

    public function getTargets(){
        return self::CLASS_CONSTRAINT;  // recuperar el valor de la entidad
    }

}
