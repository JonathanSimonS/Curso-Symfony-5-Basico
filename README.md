APP LISTA DE TAREAS
Práctica básica sobre como trabajar con Symfony (curso sobre v5, actualizado a v6)

COMANDOS PARA CONSOLA
php bin/console list make -> lista de tareas de make bundle





OTRO



// CREO LA FUNCION CREAR
    #[Route('/tarea/crear', name: 'app_crear_tarea')]
    // Manag... le dice a Symfony que inyecte el servicio Doctrine en el método del controlador.
    public function crear(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {

        $tarea = new Tarea();

        // obtenemos la descripcion mediante request | query si fuese GET ($request->query->get('descripcion');)
        $descripcion = $request->request->get('descripcion', null); // si no existe devuelve null        

        if (null !== $descripcion) {
            if (!empty($descripcion)) {

                //obtiene el objeto administrador de entidades de Doctrine, que es el objeto más importante de Doctrine.
                //responsable de guardar y recuperar objetos de la base de datos.
                $em = $doctrine->getManager(); //entityManager

                $tarea->setDescripcion($descripcion);

                // guarda la tarea
                $em->persist($tarea);

                // ejecuta un INSERT
                $em->flush();

                // mensaje flash
                $this->addFlash(
                    'success',
                    '¡Tarea creada correctamente!'
                );

                // finamente la redirijo al listado
                return $this->redirectToRoute('app_listado_tarea');

            }else {
                // mensaje flash
                $this->addFlash(
                    'warning',
                    'El campo "Descripción es obligatorio"'
                );
            }
        }

        $errors = $validator->validate($tarea);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        return $this->render('tarea/crear.html.twig', [
            "tarea" => $tarea,
        ]);
    }