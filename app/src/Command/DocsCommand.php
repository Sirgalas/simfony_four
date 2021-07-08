<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class DocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('api:docs')
            ->setDescription('Generates OpenAPI docs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $swagger = 'vendor/bin/openapi';
        $source = 'src/Controller';
        $target = 'public/docs/openapi.json';

        $process = new Process([PHP_BINARY, $swagger, $source, '--output', $target]);
        $process->run(static function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $io->info('Done!');
        return Command::SUCCESS;
    }
}