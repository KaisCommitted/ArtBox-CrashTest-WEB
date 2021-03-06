<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Entity\Participant;
use App\Entity\User;
use App\Form\EvenementType;
use App\Repository\CommentEventRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Spatie\CalendarLinks\Link;
use Symfony\Component\Validator\Constraints\DateTime;
/**
 * @Route("/evenement")
 */
class EvenementController extends AbstractController
{
    /**
     * @Route("/", name="evenement_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }

    /**
     * @Route("/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {


        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


           $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);



            return $this->redirectToRoute('evenement_index');


        }



        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement,CommentEventRepository $commentEventRepository): Response
    {
        $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $comments = $commentEventRepository->findBy(['idEvent' => $evenement->getId()]);
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'comments' => $comments,
            'CurrentUser' => $CurrentUser,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evenement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evenement_delete", methods={"POST"})
     */
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_index');
    }




    /**
     * @Route("/", name="evenement_search", methods={"POST"})
     */
    public function search(EvenementRepository $evenementRepo, Request $request)
    {
        $data=$request->get('mots');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $evenementRepo->search($data);
       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }



    /**
     * @Route("/back/index", name="evenement_back_index", methods={"GET"})
     */
    public function indexback(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }

    /**
     * @Route("/back/new", name="evenement_back_new", methods={"GET","POST"})
     */
    public function newback(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_back_index');
        }

        return $this->render('evenement/backnew.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/back/{id}", name="evenement_back_show", methods={"GET"})
     */
    public function showback(Evenement $evenement): Response
    {
        return $this->render('evenement/backshow.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/back/{id}/edit", name="evenement_back_edit", methods={"GET","POST"})
     */
    public function editback(Request $request, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_back_index');
        }

        return $this->render('evenement/backedit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/back/{id}", name="evenement_back_delete", methods={"POST"})
     */
    public function deleteback(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_back_index');
    }




    /**
     * @Route("/back/", name="evenement_back_search", methods={"POST"})
     */
    public function searchback(EvenementRepository $evenementRepo, Request $request)
    {
        $data=$request->get('mots');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $evenementRepo->search($data);
       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }

    /**
     * @Route("/back/{name}", name="evenement_back_cat", methods={"GET"})
     */
    public function FindByCategorieback(EvenementRepository $evenementRepo, Categorie $categorie): Response
    {

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $evenementRepo->FindByCategorie($categorie);

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }


    /**
     * @Route("/tri/triDate", name="evenement_triDate")
     */
    public function TriDate(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.date ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/tri/triAlphabetical", name="evenement_triAlphabetical")
     */
    public function TriAlphabetical(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.nomEvent ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/tri/triTrending", name="evenement_triTrending")
     */
    public function TriTrending(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.nbMax-E.capaciteEvent DESC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }


    /**
     * @Route("/tri/triOrganizer", name="evenement_triOrganizer")
     */
    public function TriOrganizer(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.idOrg ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());

            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/filter/ThisMonth", name="evenement_ThisMonth")
     */
    public function FilterThisMonth(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<30 AND DATE_DIFF(E.date,CURRENT_DATE())>0 '
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }
    /**
     * @Route("/filter/Today", name="evenement_Today")
     */
    public function FilterToday(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())=0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/filter/ThisWeek", name="evenement_ThisWeek")
     */
        public function FilterThisWeek(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery(
                'SELECT E FROM App\Entity\Evenement E 
                WHERE DATE_DIFF(E.date,CURRENT_DATE())<7 AND DATE_DIFF(E.date,CURRENT_DATE())>0'
            );

            $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


           $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
                ->getRepository(Categorie::class)
                ->findAll();

            return $this->render('evenement/index.html.twig', [
                 'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
            ]);

        }

    /**
     * @Route("/filter/Upcoming", name="evenement_Upcoming")
     */
    public function FilterUpcoming(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())>0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/filter/HasPassed", name="evenement_HasPassed")
     */
    public function FilterHasPassed(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }



    /**
     * @Route("/TriCat/show", name="evenement_cat", methods={"POST"})
     */
    public function FindByCategorie(EntityManagerInterface $em,Request $request): Response
    {
        $data=$request->get('myText');
        $queryBuilder = $em->getRepository(Evenement::class)->createQueryBuilder('E');
        $queryBuilder->andWhere('E.categorie = :cat');
        $queryBuilder->setParameter('cat', $data);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $queryBuilder->getQuery()->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);
    }







    /**
     * @Route("/back/tri/triDate", name="evenement_back_triDate")
     */
    public function TriBackDate(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.date ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/tri/triAlphabetical", name="evenement_back_triAlphabetical")
     */
    public function TriBackAlphabetical(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.nomEvent ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/tri/triTrending", name="evenement_back_triTrending")
     */
    public function TriBackTrending(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.nbMax-E.capaciteEvent DESC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();

       
       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/tri/triOrganizer", name="evenement_back_triOrganizer")
     */
    public function TriBackOrganizer(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.idOrg ASC'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/filter/ThisMonth", name="evenement_back_ThisMonth")
     */
    public function FilterBackThisMonth(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<30 AND DATE_DIFF(E.date,CURRENT_DATE())>0 '
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }
    /**
     * @Route("/back/filter/Today", name="evenement_back_Today")
     */
    public function FilterBackToday(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())=0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/filter/ThisWeek", name="evenement_back_ThisWeek")
     */
    public function FilterBackThisWeek(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
                WHERE DATE_DIFF(E.date,CURRENT_DATE())<7 AND DATE_DIFF(E.date,CURRENT_DATE())>0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/filter/Upcoming", name="evenement_back_Upcoming")
     */
    public function FilterBackUpcoming(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())>0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/back/filter/HasPassed", name="evenement_back_HasPassed")
     */
    public function FilterBackHasPassed(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0'
        );

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0 ORDER BY E.ratingEvent DESC'
        );
        $MostSuccesful = $query->getResult();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            var_dump($evenement);
//            die();
            $evenement->upload();
            $evenement->setCapaciteEvent($evenement->getNbMax());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();


            $path= $this->CalendarRedirect($evenement);
            $session= $this->get('session');
            $session->set('path',$path);


            return $this->redirect($session->get('path'));


        }
        $evenements = $query->getResult();


       $CurrentUser =$this->getDoctrine()
            ->getRepository(User::class)->findOneBy(['username' => 'kais']);
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('evenement/backindex.html.twig', [
             'evenements' => $evenements,'categories' => $categories,'form' => $form->createView(),'form' => $form->createView(),'CurrentUser' => $CurrentUser,'form' => $form->createView(),'CurrentUser' => $CurrentUser,'MostSuccesful' => $MostSuccesful,
        ]);

    }

    public function CalendarRedirect(Evenement $evenement)
    {
        $eventDate = $evenement->getDate();

        $from =\DateTime::createFromFormat('Y-m-d', $eventDate->format('Y-m-d'));
        $to = \DateTime::createFromFormat('Y-m-d', $eventDate->format('Y-m-d'));

        $link = Link::create($evenement->getNomEvent(), $from, $to)
            ->description($evenement->getDescription())
            ->address( $evenement->getLocationEvent());
        echo $link->google();
        $path =  $link->google();
        return $path;
        // return $this->redirect($link->google());

    }






    public function UpdateJoin(Evenement $evenement)
    {
        $part = $evenement->getCapaciteEvent();
        $part = $part -1 ;
        $evenement->setCapaciteEvent($part);

        $this->getDoctrine()->getManager()->flush();


    }

    public function UpdateCancel(Evenement $evenement)
    {
        $part = $evenement->getCapaciteEvent();
        $part = $part +1 ;
        $evenement->setCapaciteEvent($part);

        $this->getDoctrine()->getManager()->flush();


    }

    public function UpdateRating(Evenement $evenement,int $sum,int $total)
    {

        $NewRating = $sum / $total ;

        $evenement->setRatingEvent($NewRating);

        $this->getDoctrine()->getManager()->flush();


    }

}
