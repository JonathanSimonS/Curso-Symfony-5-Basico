<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Repository\TareaRepository;
use App\Service\TareaManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TareaController extends AbstractController
{

    // #[Route('/tarea', name: 'app_tarea')]
    // public function index(): Response
    // {
    //     return $this->render('tarea/index.html.twig', [
    //         'controller_name' => 'TareaController',
    //     ]);
    // }

    // CREO LA FUNCION PARA LISTAR
    #[Route('/', name: 'app_listado_tarea')]
    public function listado(TareaRepository  $tareaRepository): Response
    {
        $tareas = $tareaRepository->findAll();      // obtenemos las tareas
        return $this->render('tarea/listado.html.twig', [
            'tareas' => $tareas,
        ]);
    }

    // CREO LA FUNCION CREAR
    /**
     * @Route("/crear-tarea", name="app_crear_tarea")
     */
    public function crear(Request $request, ManagerRegistry $doctrine,): Response
    {

        $descripcion = $request->request->get('descripcion', null);
        $tarea = new Tarea();
        if (null !== $descripcion) {
            if (!empty($descripcion)) {
                $em = $doctrine->getManager(); //entityManager
                $tarea->setDescripcion($descripcion);
                $em->persist($tarea);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Tarea creada correctamente!'
                );
                return $this->redirectToRoute('app_listado_tarea');
            } else {
                $this->addFlash(
                    'warning',
                    'El campo "Descripción es obligatorio"'
                );
            }
        }
        return $this->render('tarea/crear.html.twig', [
            "tarea" => $tarea,
        ]);
    }

    // CREO LA FUNCION EDITAR
    #[Route('/tarea/editar/{id}', name: 'app_editar_tarea')]
    // en los parámetros indicar primero los nuestros (apreciación)
    // editando una entidad, por lo que la debemos recoger con TareaRepository
    public function editar(int $id, TareaRepository $tareaRepository, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {

        // busca por nombre y precio(uno)
        //$tarea = $tareaRepository->findOneBy(['name' => 'Keyboard','price' => 1999,]);

        // busca variosque coincidan con el nombre, ordenados por precio
        //$tarea = $tareaRepository->findBy(['name' => 'Keyboard'],['price' => 'ASC']);
        
        $tarea = $tareaRepository->find($id);

        // si no se encuentra lanzamos excepcion
        if (null === $tarea) {
            throw $this->createNotFoundException();
        }

        // obtenemos la descripcion mediante request | query si fuese GET ($request->query->get('descripcion');)
        $descripcion = $request->request->get('descripcion', null); // si no existe devuelve null        

        if (null !== $descripcion) {
            if (!empty($descripcion)) {

                //obtiene el objeto administrador de entidades de Doctrine, que es el objeto más importante de Doctrine.
                //responsable de guardar y recuperar objetos de la base de datos.
                $em = $doctrine->getManager(); //entityManager

                $tarea->setDescripcion($descripcion);

                // ejecuta un INSERT
                $em->flush();

                // mensaje flash
                $this->addFlash('success','¡Tarea editada correctamente!' );

                // finamente la redirijo al listado
                return $this->redirectToRoute('app_listado_tarea');

            }else {
                // mensaje flash
                $this->addFlash(
                    'warning',
                    'El campo "Descripción" es obligatorio'
                );
            }
        }

        $errors = $validator->validate($tarea);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        return $this->render('tarea/editar.html.twig', [
            "tarea" => $tarea,
        ]);
    }

    // CREO LA FUNCION ELIMINAR
    /**
     * @Route(
     * "/tarea/eliminar/{id}", 
     * name="app_eliminar_tarea", 
     * requirements={"id"="\d+"}
     * )
     */
    public function eliminar(Tarea $tarea,ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager(); //entityManager
        $em -> remove($tarea);
        $em ->flush(); // para que se ejcute en bbdd
        $this->addFlash(
            'danger',
            '¡Tarea eliminada correctamente!'
        );
        // solo redirigimos
        return $this->redirectToRoute('app_listado_tarea');
    }

    //************ SERVICIOS  ************//
    
    /**
     * @Route("/crear-tarea-servicio", name="app_crear_tarea_servicio")
     */
    public function crearServicio(TareaManager $tareaManager, Request $request): Response
    {
        $descripcion = $request->request->get('descripcion', null);
        $tarea = new Tarea();
        if (null !== $descripcion) {
            $tarea->setDescripcion($descripcion);
            $errores = $tareaManager->validar($tarea);

            // if (empty($errores)) no funcionaría porque no estaría vacío
            if (0 === count($errores)) {
                $tareaManager->crear($tarea);
                $this->addFlash(
                    'success',
                    'Tarea creada correctamente!'
                );
                return $this->redirectToRoute('app_listado_tarea');
            } else {
                foreach ($errores as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('tarea/crear.html.twig', [
            "tarea" => $tarea,
        ]);
    }


    /**
     * @Route(
     * "/tarea/editar-servicio/{id}", 
     * name="app_editar_tarea_servicio", 
     * requirements={"id"="\d+"}
     * )
     */
    
    // en los parámetros indicar primero los nuestros (apreciación)
    // editando una entidad, por lo que la debemos recoger con TareaRepository

    // FORMA RECOMENDADA
    // automáticamente nos busca la tarea con id idéntica, sin pasarselo por parámetro
    public function editarConParamsConvertServicio(TareaManager $tareaManager, Tarea $tarea, Request $request): Response
    {

        $descripcion = $request->request->get('descripcion', null);
        if (null !== $descripcion) {
            $tarea->setDescripcion($descripcion);
            $errores = $tareaManager->validar($tarea);

            if (0 === count($errores)) {
                $tareaManager->crear($tarea);
                $this->addFlash(
                    'success',
                    'Tarea editada correctamente!'
                );
                return $this->redirectToRoute('app_listado_tarea');
            } else {
                foreach ($errores as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('tarea/editar.html.twig', [
            "tarea" => $tarea,
        ]);
    }

    // CREO LA FUNCION ELIMINAR CON SERVICIO USANDO TAREAMANAGER
    /**
     * @Route(
     * "/tarea-eliminar-servicio/{id}", 
     * name="app_eliminar_tarea_servicio", 
     * requirements={"id"="\d+"}
     * )
     */
    public function eliminarServicio(Tarea $tarea,TareaManager $tareaManager): Response
    {
        $tareaManager->eliminar($tarea);
        $this->addFlash(
            'danger',
            '¡Tarea eliminada correctamente!'
        );
        // solo redirigimos
        return $this->redirectToRoute('app_listado_tarea');
    }

}