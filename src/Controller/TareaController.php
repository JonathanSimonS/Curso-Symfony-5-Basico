<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Repository\TareaRepository;
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
    #[Route('/tarea/eliminar/{id}', name: 'app_eliminar_tarea')]
    public function eliminar(int $id): Response
    {
        return $this->render('tarea/eliminar.html.twig', [
            'controller_name' => 'TareaController',
        ]);
    }
}
