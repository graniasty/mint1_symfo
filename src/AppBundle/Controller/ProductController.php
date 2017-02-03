<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProductController extends Controller
{
    /**
     * @Route("list")
     */
    public function listAction()
    {
        $products = $this->getProducts();
        return $this->render('AppBundle:Product:list.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/{id}/add-to-cart")
     */
    public function addToCartAction($id)
    {
        if(!$product = $this->getProduct($id))
        {
            throw $this->createNotFoundException('Produkt nie znaleziony');
        }

        $session = $this->get('session');

        $basket = $session->get('basket', array());

        if(!array_key_exists($id, $basket))
        {
            $basket[$id] = [
                'id' => $id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];
        } else {
            $basket[$id]['quantity']++;
        }

        $session->set('basket', $basket);

        $this->addFlash('success', 'Produkt został pomyślnie dodany');


        return $this->redirectToRoute('app_product_basket');
    }

    /**
     * @Route("/basket")
     */
    public function basketAction()
    {
        $products = $this->get('session')->get('basket',[]);

        return $this->render('AppBundle:Product:basket.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/{id}/remove-from-cart")
     */
    public function removeFromCartAction($id)
    {
        $session = $this->get('session');

        $basket = $session->get('basket');

        unset ($basket[$id]);

        $session->set('basket', $basket);

        $this->addFlash('success', 'Produkt został usunięty z koszyka');

        return $this->render('AppBundle:Product:basket.html.twig', array(

            'products' => $basket

        ));
    }

    /**
     * @Route("/clearBasket")
     */
    public function clearBasketAction()
    {
        $session = $this->get('session');

        $session->set('basket', array());

        $products = array();

        return $this->render('AppBundle:Product:basket.html.twig', array(

            'products' => $products

        ));
    }

    private function getProducts()
    {
        $file = file('products.txt');
        $products = array();
        foreach ($file as $p){
            $e = explode(':', trim($p));
            $products[$e[0]] = array(
                'id' => $e[0],
                'name' => $e[1],
                'price' => $e[2],
                'description' => $e[3]
            );
        }

        return $products;
    }

    private function getProduct($id)
    {
        $products = $this->getProducts();

        if (array_key_exists($id, $products))
        {
            return $products[$id];
        }

        return null;
    }

}
