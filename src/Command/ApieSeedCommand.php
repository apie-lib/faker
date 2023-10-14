<?php
namespace Apie\Faker\Command;

use Apie\Common\ApieFacade;
use Apie\Common\Wrappers\GeneralServiceFactory;
use Apie\Core\Actions\BoundedContextEntityTuple;
use Apie\Core\Attributes\FakeCount;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Faker\Seeders\ApieResourceSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'apie:seed-entities', description: 'Seeds the Apie datalayer with faked domain objects.')]
class ApieSeedCommand extends Command
{
    public function __construct(
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly ApieFacade $apieFacade
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('amount', 'a', InputOption::VALUE_REQUIRED, description: 'How many objects should be created', default: 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seeders = [];
        /** @var BoundedContextEntityTuple $tuple */
        foreach ($this->boundedContextHashmap->getTupleIterator() as $tuple) {
            $counter = $input->getOption('amount');
            $attributes = $tuple->resourceClass->getAttributes(FakeCount::class);
            foreach ($attributes as $attribute) {
                $counter = $attribute->newInstance()->count;
            }
            $seeders[] = new ApieResourceSeeder(
                $tuple,
                $counter
            );
        }
        $generator = GeneralServiceFactory::createFaker($seeders);
        foreach ($seeders as $seeder) {
            $index = 0;
            $output->writeln($seeder->getResourceClass()->getShortName());
            do {
                $resource = $seeder->getResource($generator, $index);
                if ($resource) {
                    $this->apieFacade->persistNew($resource, $seeder->getBoundedContextId());
                }
                $index++;
            } while ($resource);
        }
        return Command::SUCCESS;
    }
}
