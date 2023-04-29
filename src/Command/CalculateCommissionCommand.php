<?php

namespace App\Command;

use App\Commission\CommissionProvider;
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
        private CommissionProvider $commissionProvider,
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

            $commissionRate = $this->commissionProvider->getRateForTransaction($transaction);

            $rate = $this->rateProvider->getRate('EUR', $transaction->getCurrency());

            if ($rate === 0) {
                throw new \RuntimeException('Exchange rate can not be zero!');
            }
            $amountInEUR = $transaction->getAmount() / $rate;
            $commission = $amountInEUR * $commissionRate;

            if ($output->isVerbose()) {
                $output->writeln(\sprintf(
                    '%g %s = %g %5$s -> %g %s (%d%%)',
                    round($transaction->getAmount(), 2),
                    $transaction->getCurrency(),
                    round($amountInEUR, 2),
                    round($commission, 2),
                    'EUR',
                    $commissionRate * 100,
                ));
            } else {
                $output->writeln($commission);
            }
        }

        return Command::SUCCESS;
    }
}
