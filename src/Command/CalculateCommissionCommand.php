<?php

namespace App\Command;

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
    public function __construct(private Filesystem $filesystem)
    {
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

            $binResults = \file_get_contents('https://lookup.binlist.net/' .$transaction->getBin());
            if (!$binResults) {
                die('error!');
            }
            $r = \json_decode($binResults);
            $isEu = self::isEu($r->country->alpha2);

            $rate = @\json_decode(file_get_contents('https://api.exchangeratesapi.io/latest'), true)['rates'][$transaction->getCurrency()];

            if ($transaction->getCurrency() == 'EUR' || $rate == 0) {
                $amntFixed = $transaction->getAmount();
            }
            if ($transaction->getCurrency() != 'EUR' && $rate > 0) {
                $amntFixed = $transaction->getAmount() / $rate;
            }

            $output->writeln($amntFixed * ($isEu ? 0.01 : 0.02));
        }

        return Command::SUCCESS;
    }

    private static function isEu($c): bool
    {
        switch ($c) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                return true;
            default:
                return false;
        }
    }
}
