<?php

namespace App\Tests\Command;

use App\Entity\Beer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ImportBeersCommandTest extends KernelTestCase
{
    /**
     * @var ?EntityManager
     */
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->expects(self::any())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class));

        $command = $application->find('app:import:beers');
        $command->setClient($clientMock);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertStringContainsString('Done!', $commandTester->getDisplay());
    }

    /**
     * @dataProvider beerCorrectData
     *
     * @param $beerCorrectData
     */
    public function testCorrectDataImport($beerCorrectData): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $responceMock = $this->createMock(ResponseInterface::class);
        $responceMock->method('toArray')->willReturn($beerCorrectData);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->expects(self::any())
            ->method('request')
            ->willReturn($responceMock);

        $command = $application->find('app:import:beers');
        $command->setClient($clientMock);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        /** @var Beer $newBeer */
        $newBeer = $this->entityManager->getRepository(Beer::class)->findOneByName($beerCorrectData[0]['name']);
        self::assertEquals($newBeer->getName(), $beerCorrectData[0]['name']);
        self::assertEquals($newBeer->getPrice(), $beerCorrectData[0]['price']);
        self::assertEquals($newBeer->getBrewer()->getName(), $beerCorrectData[0]['brewer']);
        self::assertEquals($newBeer->getCountry(), $beerCorrectData[0]['country']);
        self::assertEquals($newBeer->getCapacityMilliliter(), 355);
        self::assertEquals($newBeer->getPricePerLitre(), 42.23);
        self::assertEquals($newBeer->getType(), $beerCorrectData[0]['type']);
        self::assertStringContainsString('Done!', $commandTester->getDisplay());
    }

    /**
     * @dataProvider beerLackData
     *
     * @param $beerLackData
     */
    public function testImport($beerLackData): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $responceMock = $this->createMock(ResponseInterface::class);
        $responceMock->method('toArray')->willReturn($beerLackData);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->expects(self::any())
            ->method('request')
            ->willReturn($responceMock);

        $command = $application->find('app:import:beers');
        $command->setClient($clientMock);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::assertStringContainsString('Lack of data', $commandTester->getDisplay());
    }

    public function beerCorrectData()
    {
        return [[[[
            'product_id' => 2,
            'name' => 'Mad Jack Mixer Superb Exclusive',
            'size' => '12  ×  Can 355 ml',
            'price' => '14.99',
            'beer_id' => 1,
            'image_url' => 'http://www.thebeerstore.ca/sites/default/files/styles/brand_hero/public/sbs/brand/18636-MJ-Family-Can-TBS-322x344.jpg?itok=v_mQRmR1',
            'category' => 'Domestic Specialty',
            'abv' => '5.0',
            'style' => 'N/A',
            'attributes' => 'N/A',
            'type' => 'Lager',
            'brewer' => 'Molson',
            'country' => 'Canada',
            'on_sale' => false,
        ]]]];
    }

    public function beerLackData()
    {
        return [[[[
            'product_id' => 2,
            'name' => null,
            'size' => '12  ×  Can 355 ml',
            'price' => '14.99',
            'beer_id' => 1,
            'image_url' => 'http://www.thebeerstore.ca/sites/default/files/styles/brand_hero/public/sbs/brand/18636-MJ-Family-Can-TBS-322x344.jpg?itok=v_mQRmR1',
            'category' => 'Domestic Specialty',
            'abv' => '5.0',
            'style' => 'N/A',
            'attributes' => 'N/A',
            'type' => 'Lager',
            'brewer' => 'Molson',
            'country' => 'Canada',
            'on_sale' => false,
        ]]]];
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
