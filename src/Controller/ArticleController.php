<?php
/**
 * Created by PhpStorm.
 * User: Yuliya
 * Date: 18/08/2019
 * Time: 10:22
 */

namespace App\Controller;


use App\Entity\Post;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController
{
    private $serializer;
    private $em;
    public function __construct(EntityManagerInterface $em , SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @Route("/articles", name="article_list")
     * @Method("GET")
     * @return Response
     */
    public function list(){
        $articles = $this->em->getRepository(Post::class)->findAll();
        $data = $this->serializer->serialize($articles, 'json',
            SerializationContext::create()->setGroups(array('list')));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/articles/{id}", name="article_show")
     * @param Post $article
     * @return Response
     */
    public function show(Post $article)
    {

        $data = $this->serializer->serialize($article, 'json',
            SerializationContext::create()->setGroups(array('detail')));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/articles", name="article_create")
     * @Method("POST")
     */
    public function create(Request $request){
        $data = $request->getContent();
        $article = $this->serializer->deserialize($data, Post::class, 'json');
        $this->em->persist($article);
        $this->em->flush();
        return new Response('', Response::HTTP_CREATED);
    }
}