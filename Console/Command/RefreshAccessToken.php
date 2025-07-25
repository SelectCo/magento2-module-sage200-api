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

    public function __construct(SageToken $sageToken, Data $helper)
    {
        $this->sageToken = $sageToken;
        $this->helper = $helper;
        parent::__construct();
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

        if ($this->sageToken->refreshToken()) {
            $output->writeln('<info>Access token was refreshed successfully!</info>');
        } else {
            $output->writeln('<error>Access token could not be refreshed!</error>');
            $exitCode = 1;
        }

        if ($result = $this->sageToken->checkAccessTokenExpiry()) {
            $output->writeln("<info>Access token expires in " . round($result['hoursToExpire'], 2) . " hours, at {$result['expiryDate']} </info>");
        } else {
            $output->writeln('<error>Access token has expired!</error>');
            $exitCode = 1;
        }

        if ($result = $this->sageToken->checkRefreshTokenExpiry()) {
            $output->writeln("<info>Refresh token expires in " . round($result['daysToExpire'], 2) . " days, at {$result['expiryDate']} </info>");
        } else {
            $output->writeln('<error>Refresh token has expired!</error>');
            $exitCode = 1;
        }

        return $exitCode;
    }
}
