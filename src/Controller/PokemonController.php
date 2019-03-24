<?php

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
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     *     requirements="\d+",
     *     default="60",
     *     description="Max number of pokemon per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     *
     * @Rest\QueryParam(
     *     name="type",
     *     description="Type of Pokemon"
     * )
     *
     * @Rest\View
     * @return Pokemons
     */
    public function list(): Pokemons
    {
        $pager = $this->getDoctrine()->getRepository(Pokemon::class)->search(
            $this->fetcher->get('keyword'),
            $this->fetcher->get('order'),
            $this->fetcher->get('limit'),
            $this->fetcher->get('offset'),
            $this->fetcher->get('type')
        );

        return new Pokemons($pager);
    }

    /**
     * @Rest\Get(
     *     name="pokemon_show",
     *     requirements={"id"="\d+"},
     *     path="/{id}",
     * )
     *
     * @Rest\View
     *
     * @param int $id
     * @param PokemonRepository $pokemonRepository
     * @return mixed
     */
    public function show(int $id, PokemonRepository $pokemonRepository)
    {
        $pokemon = $pokemonRepository->findOneById($id);

        if (is_null($pokemon)) {
            throw new NotFoundHttpException('Pokemon not found !');
        }

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
     * @return \FOS\RestBundle\View\View
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
     * @param int $id
     * @param PokemonRepository $pokemonRepository
     * @return \FOS\RestBundle\View\View
     */
    public function edit(Request $request, int $id, PokemonRepository $pokemonRepository): View
    {
        $data = $this->serializer->deserialize($request->getContent(), 'array', 'json');

        $pokemon = $pokemonRepository->findOneById($id);

        if (is_null($pokemon)) {
            throw new NotFoundHttpException('Pokemon not found !');
        }

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
     * @param int $id
     * @param PokemonRepository $pokemonRepository
     */
    public function remove(int $id, PokemonRepository $pokemonRepository): void
    {
        $pokemon = $pokemonRepository->findOneById($id);

        if (is_null($pokemon)) {
            throw new NotFoundHttpException('Pokemon not found !');
        }

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
