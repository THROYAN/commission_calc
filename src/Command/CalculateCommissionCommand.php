<?php

namespace App\Command;

use App\Country\CountryProviderInterface;
use App\Currency\RateProviderInterface;
use App\Model\Transaction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'commission:calc')]
class CalculateCommissionCommand extends Command
{
    public function __construct(
        private Filesystem $filesystem,
        private CountryProviderInterface $countryProvider,
        private RateProviderInterface $rateProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'File with transaction JSONs separated by new line')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!$this->filesystem->exists($file)) {
            throw new \RuntimeException('File not found');
        }

        $rows = explode("\n", \file_get_contents($file));

        foreach ($rows as $row) {
            if (empty($row)) {
                break;
            }
            $transaction = Transaction::fromJSON($row);

            $country = $this->countryProvider->getCountryForTransaction($transaction);

            $rate = $this->rateProvider->getRate($transaction->getCurrency(), 'EUR');

            $amntFixed = $transaction->getAmount() * $rate;

            $output->writeln($amntFixed * ($country->isEU() ? 0.01 : 0.02) . ' (' . $rate . ')');
        }

        return Command::SUCCESS;
    }
}
