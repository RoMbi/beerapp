<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Entity\Beer;
use App\Entity\Brewer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BrewerControllerTest
 *
 * @package App\Tests\App\Controller
 */
class BrewerControllerTest extends WebTestCase
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
        $kernel = self::bootKernel();
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
     * @dataProvider beers
     */
    public function testBrewerGet(array $beers): void
    {
        $client = static::createClient();

        $brewer = $this->entityManager->getRepository(Brewer::class)->findOneBy(['name' => 'Tyskie']);
        $brewer = $brewer ?: new Brewer();
        $brewer->setName('Tyskie');

        /** @var Beer $beer */
        foreach ($beers as $beer) {
            $this->entityManager->persist($beer);
        }
        $this->entityManager->flush();

        $client->request('GET', '/api/brewers');
        $brewerRepository = $this->entityManager->getRepository(Brewer::class);

        $serializedEntity = $this->serializer->serialize(
            $brewerRepository->findAllWithBeerCount(),
            'json',
            [
                'json_encode_options' => JSON_UNESCAPED_SLASHES
            ]
        );

        self::assertEquals(
            View::create($serializedEntity)->getData(),
            $client->getResponse()->getContent()
        );
    }

    public function beers(): array
    {
        return [[[
            $this->getBeerWithNameAndExtId('Beer 1', 4),
            $this->getBeerWithNameAndExtId('Beer 2', 23),
        ]]];
    }

    /**
     * @param string $name
     * @param int $externalId
     *
     * @return Beer
     */
    private function getBeerWithNameAndExtId(string $name, int $externalId): Beer
    {
        $price = 12.32;
        $capacity = 450;
        $beer = new Beer();
        $beer->setName($name);
        $beer->setPrice($price);
        $beer->setExternalId($externalId);
        $beer->setType('Vermont IPA');
        $beer->setCountry('Poland');
        $beer->setCapacityMilliliter($capacity);
        $beer->setPricePerLitre(($price / $capacity) * 1000);

        return $beer;
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
