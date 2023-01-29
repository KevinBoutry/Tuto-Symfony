<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;


#[Route("/todo")]
class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        // Afficher notre tableau de todo
        // Si j'ai mon tableau de todo dans la sesion je ne fais que l'afficher
        if(!$session->has('todolist'))
        {
            $todolist = [
                'achat' => 'acheter du pain',
                'cours' => 'apprendre Symfony',
                'correction' => 'frapper mon fils',
                'repas' => 'un bon gros kebab'
            ];
            $session->set('todolist', $todolist);
            $this->addFlash('info',"La liste des Todos vient d'être initialisée");
        }
        // Sinon je l'initialise puis je l'affiche
        return $this->render('todo/index.html.twig');
    }

    #[Route('/add/{name?defaultName}/{content?defaultContent}', name: 'todo.add',)]
    public function addTodo(Request $request, $name, $content) : RedirectResponse
    {
        // Vérifier si j'ai mon tableau dans la session
        $session = $request->getSession();
        if($session->has('todolist')){
            // Si oui Vérifier Si on a deja un todo avec le même nom
            $todos = $session->get('todolist');
            if (isset($todos[$name]))
            {
                // Si oui : Afficher erreur
                $this->addFlash('error',"$name est déjà présent dans la liste");
            }
            // Si non, on ajoute et on affiche un message de succès
            else
            {
                $todos[$name] = $content;                
                $this->addFlash('success',"$name a bien été ajouté à la liste");
                $session->set('todolist', $todos);
            }      
        }
        // Si non
        // Afficher une erreur et on va rediriger vers le controlleur index
        else
        {
            $this->addFlash('error',"La liste des todos n'est pas encore initialisées");
        }
        return $this->redirectToRoute('app_todo');
    }

    #[Route('/remove/{name}', name: 'todo.remove')]
    public function removeTodo(Request $request, $name) : RedirectResponse
    {
        $session = $request->getSession();
        if($session->has('todolist'))
            $todos = $session->get('todolist');
            if (isset($todos[$name]))
            {
                unset($todos[$name]);
                $this->addFlash('success', "l'entrée $name a bien été supprimée");
                $session->set('todolist', $todos);
            }
            else
                $this->addFlash('error', "l'entrée $name n'existe pas");
        return $this->redirectToRoute('app_todo');
    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content) : RedirectResponse
    {
        $session = $request->getSession();
        if($session->has('todolist'))
            $todos = $session->get('todolist');
            if (isset($todos[$name]))
            {
                $todos[$name] = $content;
                $this->addFlash('success', "l'entrée $name a bien été mise à jour");
                $session->set('todolist', $todos);
            }
            else
                $this->addFlash('error', "l'entrée $name n'existe pas");
        return $this->redirectToRoute('app_todo');
    }
}