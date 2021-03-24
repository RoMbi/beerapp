<?php

namespace App\Command;

use App\Entity\Beer;
use App\Entity\Brewer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TypeError;

class ImportBeersCommand extends Command
{
    protected static $defaultName = 'app:import:beers';

    /** @var HttpClientInterface */
    private HttpClientInterface $client;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * ImportBeersCommand constructor.
     *
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $importLogger
     */
    public function __construct(HttpClientInterface $client,
                                EntityManagerInterface $entityManager,
                                LoggerInterface $importLogger)
    {
        parent::__construct();

        $this->client = $client;
        $this->em = $entityManager;
        $this->logger = $importLogger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports beers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $response = $this->client->request(
                'GET',
                'http://ontariobeerapi.ca/beers/'
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Import - can\'t connect to API');
            $output->writeln("Can\'t connect to API");
            return 1;
        }

        $output->writeln("Importing...");

        $results = $response->toArray();
        $progressBar = new ProgressBar($output, count($results));

        foreach ($results as $importedBeer) {
            try {
                $beer = $this->provideFilledBeer($importedBeer);

                $progressBar->advance();

                $this->em->persist($beer);
            } catch (TypeError $e) {
                $error = sprintf('Lack of data in beer external id %s, error: %s', $importedBeer['beer_id'], $e->getMessage());
                $output->writeln($error);
                $this->logger->error($error);
            } catch (Exception $e) {
                $error = sprintf('Something went wrong when importing, beer external id %s, error: %s', $importedBeer['beer_id'], $e->getMessage());
                $output->writeln($error);
                $this->logger->error($error);
            }
        }

        $this->em->flush();
        $output->writeln("Done!");

        return 0;
    }

    /**
     * @param int $beerExternalId
     *
     * @return Beer
     */
    private function getBeer(int $beerExternalId): Beer
    {
        $beer = $this->em->getRepository(Beer::class)->findOneBy(['externalId' => $beerExternalId]);
        $beer = $beer ?: new Beer();
        $beer->setExternalId($beerExternalId);

        return $beer;
    }

    /**
     * @param string $brewerName
     *
     * @return Brewer
     */
    private function getBrewer(string $brewerName): Brewer
    {
        $brewer = $this->em->getRepository(Brewer::class)->findOneBy(['name' => $brewerName]);
        $brewer = $brewer ?: new Brewer();
        $brewer->setName($brewerName);
        $this->em->persist($brewer);
        $this->em->flush();

        return $brewer;
    }

    public function setClient(HttpClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @param array $importedBeer
     *
     * @return Beer
     */
    protected function provideFilledBeer(array $importedBeer): Beer
    {
        $beer = $this->getBeer($importedBeer['beer_id']);
        $beer->setName($importedBeer['name']);
        $beer->setPrice($importedBeer['price']);
        $beer->setType($importedBeer['type']);
        $beer->setBrewer($this->getBrewer($importedBeer['brewer']));
        $beer->setCountry($importedBeer['country']);
        preg_match("/(\d+)(?=\D*$)/", $importedBeer['size'], $capacity);
        $beer->setCapacityMilliliter((int)$capacity[0]);
        $beer->setPricePerLitre(($beer->getPrice() / $beer->getCapacityMilliliter()) * 1000);

        return $beer;
    }
}
