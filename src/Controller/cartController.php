<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class cartController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $panier = $session->get('panier', []);
        $data = [];
        foreach($panier as $id =>$quantity ){
            $data[] = [
                'produit' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        
        $total =0;
        foreach($data as $item){
            $totalItem = $item['produit']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }
        $q = count($data);

        return $this->render('cart.html.twig', ['items' => $data, 'total' => $total,'q'=>$q]);
    }

    /**
     * @Route("/panier/ajouter/{produitId}", name="addPanier")
     * @param $produitId
     * @param SessionInterface $session
     * @return string
     */
    public function AjouterAuPanier($produitId, SessionInterface $session)
    {
       $panier = $session->get('panier', []);
       if(!empty($panier[$produitId])){
            $panier[$produitId]++;
       }else{
            $panier[$produitId] = 1;
       }
       $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    } 

    /**
     * @Route("/diminuer/ajouter/{produitId}", name="diminuer")
     * @param $produitId
     * @param SessionInterface $session
     * @return string
     */
    public function diminuer($produitId, SessionInterface $session)
    {
       $panier = $session->get('panier', []);

    
       if(!empty($panier[$produitId]) && $panier[$produitId]>1){
            $panier[$produitId]--;
       }
       
       $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    } 
    /**
     * @Route("/vider", name="vider")
     * @param SessionInterface $session
     */
    public function vider(SessionInterface $session)
    {
       $session->clear();
        return $this->redirectToRoute('panier');
    } 
    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     * @param $id
     * @param SessionInterface $session
     * @return string
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }


    /**
     * @Route("/produit", name="produit")
     */
    public function product()
    {
        $produits= $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('product.html.twig',[ 'produits' => $produits]);
    }

}