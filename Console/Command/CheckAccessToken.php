<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Console\Command;

use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\SageToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckAccessToken extends Command
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
        $this->setName('sage200:token:check');
        $this->setDescription('Check if access token is still valid.');
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

        $result = $this->sageToken->checkAccessTokenExpiry();
        if (!$result) {
            $output->writeln('<error>Access token has expired!</error>');
            $exitCode = 1;
        } else {
            $output->writeln("<info>Access token expires in {$result['secondsToExpire']} seconds, at {$result['expiryDate']} </info>");
        }

        $result = $this->sageToken->checkRefreshTokenExpiry();
        if (!$result) {
            $output->writeln('<error>Refresh token has expired!</error>');
            $exitCode = 1;
        } else {
            $output->writeln("<info>Refresh token expires in {$result['secondsToExpire']} seconds, at {$result['expiryDate']} </info>");
        }

        return $exitCode;
    }
}
