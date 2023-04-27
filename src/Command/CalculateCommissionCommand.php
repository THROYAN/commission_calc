<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'commission:calc')]
class CalculateCommissionCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'File with transaction JSONs separated by new line')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!\is_file($file)) {
            throw new \RuntimeException('File not found');
        }

        foreach (explode("\n", \file_get_contents($file)) as $row) {
            if (empty($row)) {
                break;
            }
            $value = \json_decode($row, true);

            $binResults = \file_get_contents('https://lookup.binlist.net/' .$value['bin']);
            if (!$binResults) {
                die('error!');
            }
            $r = \json_decode($binResults);
            $isEu = self::isEu($r->country->alpha2);

            $rate = @\json_decode(file_get_contents('https://api.exchangeratesapi.io/latest'), true)['rates'][$value['currency']];

            if ($value['currency'] == 'EUR' || $rate == 0) {
                $amntFixed = $value['amount'];
            }
            if ($value['currency'] != 'EUR' && $rate > 0) {
                $amntFixed = $value['amount'] / $rate;
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
