<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Type;
use App\Form\PokemonType;
use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;
use App\Representation\Pokemons;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Rest\Route("/pokemon")
 *
 * Class PokemonController
 * @package App\Controller
 */
class PokemonController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var ParamFetcher $fetcher
     */
    private $fetcher;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, ParamFetcherInterface $fetcher)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->fetcher = $fetcher;
    }

    /**
     * @Rest\Get(name="pokemon_list")
     *
     * @Rest\QueryParam(
     *     name="keyword",
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     strict=true,
     *     requirements="\d+",
     *     default=60,
     *     description="Max number of pokemon per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default=1,
     *     description="The pagination offset"
     * )
     *
     * @Rest\QueryParam(
     *     name="type",
     *     description="Type of Pokemon"
     * )
     *
     * @Rest\View
     *
     * @return Pokemons
     */
    public function list(): Pokemons
    {
        $pager = $this->getDoctrine()->getRepository(Pokemon::class)->search(
            $this->fetcher->get('keyword'),
            $this->fetcher->get('order'),
            (int)$this->fetcher->get('limit'),
            (int)$this->fetcher->get('offset'),
            $this->fetcher->get('type')
        );

        $pokemons = new Pokemons($pager);

        return $pokemons;
    }

    /**
     * @Rest\Get(
     *     name="pokemon_show",
     *     requirements={"id"="\d+"},
     *     path="/{id}",
     * )
     *
     * @Rest\View(serializerGroups={"detail"})
     *
     * @param Pokemon $pokemon
     * @return Pokemon
     */
    public function show(Pokemon $pokemon): Pokemon
    {
        return $pokemon;
    }

    /**
     * @Rest\Post(name="pokemon_post")
     *
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("pokemon", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param Pokemon $pokemon
     * @return View
     */
    public function create(Request $request, Pokemon $pokemon): View
    {
        $error = $this->validator->validate($pokemon);

        if (count($error)) {
            return $this->view($error, Response::HTTP_BAD_REQUEST);
        }

        $data = $this->serializer->deserialize($request->getContent(), 'array', 'json');

        $form = $this->get('form.factory')->create(PokemonType::class, $pokemon);

        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pokemon);
        $em->flush();

        return $this->view($pokemon);
    }

    /**
     * @Rest\Patch(
     *     name="pokemon_edit",
     *     requirements={"id"="\d+"},
     *     path="/{id}",
     * )
     *
     * @Rest\View
     *
     * @param Request $request
     * @param Pokemon $pokemon
     * @return View
     */
    public function edit(Request $request, Pokemon $pokemon): View
    {
        $data = $this->serializer->deserialize($request->getContent(), 'array', 'json');

        $form = $this->get('form.factory')->create(PokemonType::class, $pokemon);

        $form->submit($data, false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pokemon);
        $em->flush();

        return $this->view($pokemon);
    }


    /**
     * @Rest\Delete(
     *     name="pokemon_delete",
     *     requirements={"id"="\d+"},
     *     path="/{id}",
     * )
     *
     * @Rest\View
     *
     * @param Pokemon $pokemon
     */
    public function remove(Pokemon $pokemon): void
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($pokemon);
        $em->flush();
    }

    /**
     * * @Rest\Get(
     *     name="pokemon_type_list",
     *     path="/types",
     * )
     *
     * @Rest\View
     *
     * @param TypeRepository $typeRepository
     * @return Type[]
     */
    public function types(TypeRepository $typeRepository): array
    {
        return $typeRepository->findAll();
    }
}
