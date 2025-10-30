<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Console\Command;

use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\SageToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshAccessToken extends Command
{
    /**
     * @var SageToken
     */
    private $sageToken;
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        SageToken $sageToken,
        Data $helper
    ) {
        parent::__construct();
        $this->sageToken = $sageToken;
        $this->helper = $helper;
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('sage200:token:refresh');
        $this->setDescription('Refresh access token.');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        if (!$this->helper->isModuleEnabled()) {
            $output->writeln('<error>Module is not enabled!</error>');
            return $exitCode;
        }

        if ($this->sageToken->refreshAccessToken()) {
            $output->writeln('<info>Access token was refreshed successfully!</info>');
        } else {
            $output->writeln('<error>Access token could not be refreshed!</error>');
            $exitCode = 1;
        }

        return $exitCode;
    }
}
