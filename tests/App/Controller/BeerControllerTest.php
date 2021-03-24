<?php

namespace App\Tests\App\Controller;

use App\Entity\Beer;
use App\Entity\Brewer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BeerControllerTest
 *
 * @package App\Tests\App\Controller
 */
class BeerControllerTest extends WebTestCase
{
    /**
     * @var ?EntityManager
     */
    private ?EntityManager $entityManager;

    /**
     * @var SerializerInterface|null
     */
    private ?SerializerInterface $serializer;

    protected function setUp(): void
    {
        $kernel = self::bootKernel(['environment' => 'test']);
        $this->serializer = $kernel->getContainer()
            ->get('serializer');
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testBeerResponseCode(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/beer/1');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider beer
     */
    public function testBeerGet(Beer $beer): void
    {
        $client = static::createClient();
        $beerRepository = $this->entityManager->getRepository(Beer::class);

        $brewer = new Brewer();
        $brewer->setName('Tyskie' . random_int(1,2000));
        $beer->setBrewer($brewer);
        $this->entityManager->persist($beer);
        $this->entityManager->flush();

        $client->request('GET', '/api/beer/' . $beer->getId());

        $serializedEntity = $this->serializer->serialize(
            $beerRepository->findOneByName($beer->getName()),
            'json',
            [
                'groups' => 'show_beer',
                'json_encode_options' => JSON_UNESCAPED_SLASHES
            ]
        );

        self::assertNotNull($beerRepository->findOneByName($beer->getName()));
        self::assertEquals(
            '"' . addslashes(View::create($serializedEntity)->getData()) . '"',
            $client->getResponse()->getContent()
        );

    }

    public function beer(): array
    {
        $price = 12.32;
        $capacity = 450;
        $beer = new Beer();
        $beer->setName('Beer name');
        $beer->setPrice($price);
        $beer->setExternalId(32);
        $beer->setType('Vermont IPA');
        $beer->setCountry('Poland');
        $beer->setCapacityMilliliter($capacity);
        $beer->setPricePerLitre(($price / $capacity) * 1000);

        return [[$beer]];
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
